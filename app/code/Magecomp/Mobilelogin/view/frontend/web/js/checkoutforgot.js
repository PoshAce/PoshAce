 require(
        ['jquery',
         'Magento_Ui/js/modal/modal',
         "mage/template",
         'mage/translate',
         'jquery/ui',
         "mage/mage"
        ],
        function(
            $,
            modal,mageTemplate,$t
        ) {
        modalWindow: null
    
            $('body').on('click', ".remind", function() {           
                 var layout = $("#layout").val();
             var options = {
                'type': 'popup',
                'modalClass' : 'customer-login-popup '+ layout,
                'focus': '[name=username]',
                'responsive': true,
                 'responsiveClass': 'custom-slide',
                'innerScroll': true,
                'buttons': []
            };
            if ($("#customer-popup-login").length != 0) {
            var popup = modal(options, $('#customer-popup-login'));
            $("#customer-popup-login").modal("openModal");

           
            $(".authentication-dropdown").hide();
                $("#customer-popup-login").modal("openModal");
            $("#customer-popup-login").addClass("ultimate");
             $(".modal-title").text("Forgot Password");
            $(".login-section").hide();
            $(".registratio-section").hide();
            $(".mobileforgotverify").hide();
            $(".mobileforgotpassword").hide();
            $(".emailforgot").hide();
            $(".mobileforgot").show();
            $(".forgot-password-section").show();
            $(".forgotimage").show();
            $(".regimage").hide();
            $(".loginimage").hide();
            }
            return false;
            });
            $('body').on('change',"input[type=radio][name=reset-type]", function(){
                var seletedType = this.value;
                if(seletedType == "mobile") {
                    $(".emailforgot").hide();
                    $(".mobileforgot").show();
                } else {
                    $(".mobileforgot").hide();
                    $(".mobileforgotpassword").hide();
                    $(".mobileforgotverify").hide();
                    $(".emailforgot").show();
                }
            });
            
        
        });
