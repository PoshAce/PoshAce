<?php
namespace Magecomp\Chatgptaicontentpro\Model\Config;

/**
 * Class Language
 * Magecomp\Chatgptaicontentpro\Model\Config
 */
class Language implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
     protected $_options;
    public function toOptionArray()
    {
          if (!$this->_options)
        {
            $this->_options = array(
                array('label' => 'Afrikaans', 'value' => 'Afrikaans'),
                array('label' => 'Albanian', 'value' => 'Albanian'),
                array('label' => 'Arabic', 'value' => 'Arabic'),
                array('label' => 'Amharic', 'value' => 'Amharic'),
                array('label' => 'Armenian', 'value' => 'Armenian'),
                array('label' => 'Azerbaijani', 'value' => 'Azerbaijani'),
                array('label' => 'Basque', 'value' => 'Basque'),
                array('label' => 'Belarusian', 'value' => 'Belarusian'),
                array('label' => 'Bengali', 'value' => 'Bengali'),
                array('label' => 'Bosnian', 'value' => 'Bosnian'),
                array('label' => 'Bulgarian', 'value' => 'Bulgarian'),
                array('label' => 'Catalan', 'value' => 'Catalan'),
                array('label' => 'Cebuano', 'value' => 'Cebuano'),
                array('label' => 'Chichewa', 'value' => 'Chichewa'),
                array('label' => 'Chinese (Simplified)', 'value' => 'Chinese'),
                array('label' => 'Chinese (Traditional)', 'value' => 'Chinese'),
                array('label' => 'Croatian', 'value' => 'Croatian'),
                array('label' => 'Czech', 'value' => 'Czech'),
                array('label' => 'Danish', 'value' => 'Danish'),
                array('label' => 'Dutch', 'value' => 'Dutch'),
                array('label' => 'English', 'value' => 'English'),
                array('label' => 'Esperanto', 'value' => 'Esperanto'),
                array('label' => 'Estonian', 'value' => 'Estonian'),
                array('label' => 'Filipino', 'value' => 'Filipino'),
                array('label' => 'Faroese', 'value' => 'Faroese'),
                array('label' => 'Finnish', 'value' => 'Finnish'),
                array('label' => 'French', 'value' => 'French'),
                array('label' => 'Galician', 'value' => 'Galician'),
                array('label' => 'Georgian', 'value' => 'Georgian'),
                array('label' => 'German', 'value' => 'German'),
                array('label' => 'Greek', 'value' => 'Greek'),
                array('label' => 'Gujarati', 'value' => 'Gujarati'),
                array('label' => 'Haitian', 'value' => 'Haitian'),
                array('label' => 'Hausa', 'value' => 'Hausa'),
                array('label' => 'Hebrew', 'value' => 'Hebrew'),
                array('label' => 'Hindi', 'value' => 'Hindi'),
                array('label' => 'Hmong', 'value' => 'Hmong'),
                array('label' => 'Hungarian', 'value' => 'Hungarian'),
                array('label' => 'Icelandic', 'value' => 'Icelandic'),
                array('label' => 'Igbo', 'value' => 'Igbo'),
                array('label' => 'Indonesian', 'value' => 'Indonesian'),
                array('label' => 'Irish', 'value' => 'Irish'),
                array('label' => 'Italian', 'value' => 'Italian'),
                array('label' => 'Japanese', 'value' => 'Japanese'),
                array('label' => 'Javanese', 'value' => 'Javanese'),
                array('label' => 'Kannada', 'value' => 'Kannada'),
                array('label' => 'Khmer', 'value' => 'Khmer'),
                array('label' => 'Korean', 'value' => 'Korean'),
                array('label' => 'Kazakh', 'value' => 'Kazakh'),
                array('label' => 'Lao', 'value' => 'Lao'),
                array('label' => 'Latin', 'value' => 'Latin'),
                array('label' => 'Latvian', 'value' => 'Latvian'),
                array('label' => 'Lithuanian', 'value' => 'Lithuanian'),
                array('label' => 'Macedonian', 'value' => 'Macedonian'),
                array('label' => 'Malay', 'value' => 'Malay'),
                array('label' => 'Malagasy', 'value' => 'Malagasy'),
                array('label' => 'Maltese', 'value' => 'Maltese'),
                array('label' => 'Maori', 'value' => 'Maori'),
                array('label' => 'Marathi', 'value' => 'Marathi'),
                array('label' => 'Malayalam', 'value' => 'Malayalam'),
                array('label' => 'Mongolian', 'value' => 'Mongolian'),
                array('label' => 'Norwegian', 'value' => 'Norwegian'),
                array('label' => 'Nepali', 'value' => 'Nepali'),
                array('label' => 'Persian', 'value' => 'Persian'),
                array('label' => 'Polish', 'value' => 'Polish'),
                array('label' => 'Portuguese', 'value' => 'Portuguese'),
                array('label' => 'Punjabi', 'value' => 'Punjabi'),
                array('label' => 'Romanian', 'value' => 'Romanian'),
                array('label' => 'Russian', 'value' => 'Russian'),
                array('label' => 'Serbian', 'value' => 'Serbian'),
                array('label' => 'Slovak', 'value' => 'Slovak'),
                array('label' => 'Slovenian', 'value' => 'Slovenian'),
                array('label' => 'Sesotho', 'value' => 'Sesotho'),
                array('label' => 'Somali', 'value' => 'Somali'),
                array('label' => 'Sinhalese', 'value' => 'Sinhalese'),
                array('label' => 'Spanish', 'value' => 'Spanish'),
                array('label' => 'Swahili', 'value' => 'Swahili'),
                array('label' => 'Sundanese', 'value' => 'Sundanese'),
                array('label' => 'Swedish', 'value' => 'Swedish'),
                array('label' => 'Tajik', 'value' => 'Tajik'),
                array('label' => 'Tamil', 'value' => 'Tamil'),
                array('label' => 'Telugu', 'value' => 'Telugu'),
                array('label' => 'Thai', 'value' => 'Thai'),
                array('label' => 'Turkish', 'value' => 'Turkish'),
                array('label' => 'Ukrainian', 'value' => 'Ukrainian'),
                array('label' => 'Urdu', 'value' => 'Urdu'),
                array('label' => 'Uzbek', 'value' => 'Uzbek'),
                array('label' => 'Vietnamese', 'value' => 'Vietnamese'),
                array('label' => 'Welsh', 'value' => 'Welsh'),
                array('label' => 'Xhosa', 'value' => 'Xhosa'),
                array('label' => 'Yiddish', 'value' => 'Yiddish'),
                array('label' => 'Yoruba', 'value' => 'Yoruba'),
                array('label' => 'Zulu', 'value' => 'Zulu'),
            );
        }
        return $this->_options;
    }
}
