# Varnish Configuration for Magento 2
# Version: 6.0

vcl 4.1;

import std;

# Backend definition
backend default {
    .host = "localhost";
    .port = "8080";
    .first_byte_timeout = 600s;
    .probe = {
        .url = "/health_check.php";
        .timeout = 2s;
        .interval = 5s;
        .window = 10;
        .threshold = 5;
    }
}

# ACL for purging
acl purge {
    "localhost";
    "127.0.0.1";
    "::1";
}

# vcl_recv - Happens before we check if we have this in cache
sub vcl_recv {
    # Normalize the header, remove the port
    set req.http.Host = regsub(req.http.Host, ":[0-9]+", "");

    # Remove tracking parameters
    if (req.url ~ "(\?|&)(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=") {
        set req.url = regsuball(req.url, "&(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "");
        set req.url = regsuball(req.url, "\?(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "?");
        set req.url = regsub(req.url, "\?&", "?");
        set req.url = regsub(req.url, "\?$", "");
    }

    # Normalize query arguments
    set req.url = std.querysort(req.url);

    # Remove all cookies for static files
    if (req.url ~ "^/(pub/)?(media|static)/") {
        unset req.http.Cookie;
        return (pass);
    }

    # Health check
    if (req.url ~ "^/(pub/)?(health_check\.php)$") {
        return (pass);
    }

    # Purge handling
    if (req.method == "PURGE") {
        if (!client.ip ~ purge) {
            return (synth(405, "Method not allowed"));
        }
        return (purge);
    }

    # Ban handling
    if (req.method == "BAN") {
        if (!client.ip ~ purge) {
            return (synth(405, "Method not allowed"));
        }
        if (req.http.X-Magento-Tags-Pattern) {
            ban("obj.http.X-Magento-Tags ~ " + req.http.X-Magento-Tags-Pattern);
        }
        return (synth(200, "Ban added"));
    }

    # Bypass shopping cart and checkout
    if (req.url ~ "/checkout" || req.url ~ "/customer") {
        return (pass);
    }

    # Bypass admin
    if (req.url ~ "/admin" || req.url ~ "/backoffice") {
        return (pass);
    }

    # Bypass API
    if (req.url ~ "/rest/") {
        return (pass);
    }

    # Remove all marketing cookies
    if (req.http.Cookie) {
        set req.http.Cookie = ";" + req.http.Cookie;
        set req.http.Cookie = regsuball(req.http.Cookie, "; +", ";");
        set req.http.Cookie = regsuball(req.http.Cookie, ";(PHPSESSID|form_key|mage-messages|mage-cache-storage|mage-cache-storage-section-invalidation|private_content_version)=", "; \1=");
        set req.http.Cookie = regsuball(req.http.Cookie, ";[^ ][^;]*", "");
        set req.http.Cookie = regsuball(req.http.Cookie, "^[; ]+|[; ]+$", "");

        if (req.http.Cookie == "") {
            unset req.http.Cookie;
        }
    }

    # Static files caching
    if (req.url ~ "\.(jpg|jpeg|gif|png|ico|css|zip|tgz|gz|rar|bz2|pdf|txt|tar|wav|bmp|rtf|js|flv|swf|html|htm|svg|webp|woff|woff2|ttf|eot)$") {
        unset req.http.Cookie;
        return (hash);
    }

    return (hash);
}

# vcl_hash - Define what is unique about a request
sub vcl_hash {
    if (req.http.cookie ~ "X-Magento-Vary=") {
        hash_data(regsub(req.http.cookie, "^.*?X-Magento-Vary=([^;]+);*.*$", "\1"));
    }

    # Cache based on protocol
    if (req.http.X-Forwarded-Proto) {
        hash_data(req.http.X-Forwarded-Proto);
    }
}

# vcl_backend_response - Happens after reading response from backend
sub vcl_backend_response {
    # Set ban-lurker friendly custom headers
    set beresp.http.X-Url = bereq.url;
    set beresp.http.X-Host = bereq.http.host;

    # Cache 404s, 301s, 500s for a short time
    if (beresp.status == 404 || beresp.status == 301 || beresp.status == 500) {
        set beresp.ttl = 120s;
        set beresp.http.Cache-Control = "public, max-age=120";
        return (deliver);
    }

    # Enable cache for all static files
    if (bereq.url ~ "\.(jpg|jpeg|gif|png|ico|css|zip|tgz|gz|rar|bz2|pdf|txt|tar|wav|bmp|rtf|js|flv|swf|html|htm|svg|webp|woff|woff2|ttf|eot)$") {
        unset beresp.http.set-cookie;
        set beresp.ttl = 1w;
        set beresp.http.Cache-Control = "public, max-age=604800";
    }

    # Respect the Cache-Control=private header
    if (beresp.http.Cache-Control ~ "private") {
        set beresp.uncacheable = true;
        set beresp.ttl = 86400s;
        return (deliver);
    }

    # Validate if we should cache
    if (beresp.ttl <= 0s ||
        beresp.http.Set-Cookie ||
        beresp.http.Vary == "*") {
        set beresp.ttl = 120s;
        set beresp.uncacheable = true;
    }

    return (deliver);
}

# vcl_deliver - Happens when we have all the pieces to deliver
sub vcl_deliver {
    # Remove ban-lurker friendly custom headers
    unset resp.http.X-Url;
    unset resp.http.X-Host;
    unset resp.http.X-Magento-Debug;
    unset resp.http.X-Magento-Tags;
    unset resp.http.X-Powered-By;
    unset resp.http.Server;
    unset resp.http.X-Varnish;
    unset resp.http.Via;
    unset resp.http.Link;

    # Add cache hit/miss header for debugging
    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
        set resp.http.X-Cache-Hits = obj.hits;
    } else {
        set resp.http.X-Cache = "MISS";
    }
}

# vcl_hit - When we find the object in cache
sub vcl_hit {
    if (obj.ttl >= 0s) {
        return (deliver);
    }
    # Hit after TTL expiration - deliver stale and trigger background fetch
    if (std.healthy(req.backend_hint) && obj.ttl + 10s > 0s) {
        set req.http.grace = "normal(limited)";
        return (deliver);
    } else {
        return (pass);
    }
}