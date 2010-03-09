<?php
/**
 * Mailer class used to create and process email services within
 * the framework.
 *
 * @package     Creovel
 * @subpackage  Core
 * @license     http://creovel.org/license MIT License
 * @since       Class available since Release 0.1.0
 * @author      Nesbert Hidalgo
 **/
/*
    Todos:
    * finish attachments support
    * auto-load attachments
    * smtp support
    * receiving emails
*/
class ActionMailer extends ActionController
{
    public $delivery_method = 'sendmail';
    public $bcc;
    public $cc;
    public $charset = 'utf-8';
    public $from;
    public $reply_to;
    public $headers;
    public $recipients;
    public $to;  // aliase of recipients
    public $sent_on;
    public $subject;
    public $body;
    public $text;
    public $html;
    
    private $__content_type = 'text/plain';
    private $__content_transfer_encoding = '7bit';
    private $__attachments;
    private $__content;
    private $__message_boundary;
    private $__mime_boundary;
    private $__header;
    
    /**
     * Set message boundaries.
     *
     * @return void
     **/
    public function initialize_action_mailer()
    {
        // set message boundaries
        $this->__message_boundary = uniqid(rand(), true);
        $this->__mime_boundary = uniqid(rand(), true);
    }
    
    /**
     * Magic functions.
     *
     * @param string $method
     * @param mixed $args
     * @return void
     **/
    public function __call($method, $args)
    {
        switch (true) {
            case preg_match('/^create_(.+)$/', $method, $regs):
            case preg_match('/^deliver_(.+)$/', $method, $regs):
            case preg_match('/^encode_(.+)$/', $method, $regs):
                // set action
                $this->_action = $regs[1];
                
                // set/call controller & action and
                // pass arguments to child mailer class
                $this->__call_action($args);
                
                // if deliver_XXX send message
                if (preg_match('/^deliver_(.+)$/', $method)) {
                    return $this->send();
                }
                // if deliver_XXX send message
                if (preg_match('/^encode_(.+)$/', $method)) {
                    return $this->encoded();
                }
            break;
            
            default:
                $this->throw_error("Undefined action '{$method}' in <strong>{$this->to_string()}</strong>");
                break;
        }
    }
    
    /**
     * Execute action and call backs.
     *
     * @return array $args
     * @return void
     **/
    private function __call_action($args)
    {
        // initialize callback
        $this->initialize();
        
        // initialize scope fix
        $this->initialize_parents();
        
        // call before filter
        $this->before_filter();
        
        if (method_exists($this, $this->_action)) {
            call_user_func_array(array($this, $this->_action), $args);
        } else {
            $this->throw_error("Undefined action '{$this->_action}' in " .
                "<strong>{$this->to_string()}</strong>.");
        }
        
        // call before filter
        $this->after_filter();
    }
    
    /**
     * Sets email content type ['text/plain', 'text/html']
     *
     * @param string $type
     * @return string
     **/
    public function set_content_type($type = 'text/plain')
    {
        return $this->__content_type = $type;
    }
    
    /**
     * Sets email content transfer encoding.
     *
     * @param string $encoding
     * @return string
     **/
    public function set_content_transfer_encoding($encoding = '7bit')
    {
        return $this->__content_transfer_encoding = $encoding;
    }
    
    /**
     * Encodes the current email message into a string.
     *
     * @return string
     **/
    public function encoded()
    {
        $return = "To: ".$this->get_email_address($this->get_recipients())."\n";
        $return .= "Subject: ".$this->get_subject()."\n";
        return $return . $this->get_headers() . $this->get_content();
    }
    
    /**
     * Sends the current email message. Returns true on success.
     *
     * @return boolean
     **/
    public function send()
    {
        switch (true) {
            case $this->delivery_method == 'smtp':
                return false;
                break;
                
            case $this->delivery_method == 'sendmail':
                return mail(
                    $this->get_email_address($this->get_recipients()),
                    $this->get_subject(),
                    $this->get_content(),
                    $this->get_headers()
                    );
                break;
                
            case $this->delivery_method == 'test':
                return true;
                break;
                
            default:
                return false;
                break;
        }
    }
    
    /**
     * Alias to send(). Returns true on success.
     *
     * @return ActionMailer::send()
     * @return boolean
     **/
    public function deliver()
    {
        return $this->send();
    }
    
    /**
     * Checks if content type is 'text/plain'.
     *
     * @return boolean
     **/
    public function is_plain_text()
    {
        return ($this->__content_type == 'text/plain') && !$this->html;
    }
    
    /**
     * Creates header of the message and loads it into $__header.
     *
     * @return string
     **/
    public function get_headers()
    {
        $this->__header = "";
        
        if ($this->from)
            $this->__header .= "From: " .
            $this->get_email_address($this->from) . "\n";
        
        if ($this->reply_to)
            $this->__header .= "Reply-To: " .
            $this->get_email_address($this->reply_to) . "\n";
        
        if ($this->cc)
            $this->__header .= "Cc: " .
            $this->get_email_address($this->cc) . "\n";
        
        if ($this->bcc)
            $this->__header .= "Bcc: " .
            $this->get_email_address($this->bcc) . "\n";
        
        $this->__header .= "Date: " .
            date("r", strtotime(datetime($this->sent_on))) . "\n";
        
        if ($this->has_attachments()) {
            $this->__header .= 'MIME-Version: 1.0'."\n";
            $this->__header .= 'Content-Type: multipart/mixed; boundary="' .
                $this->__mime_boundary . '"' . "\n";
            $this->__header .= "--{$this->mime_boundary}"."\n";
        }
        
        if (!$this->is_plain_text())  {
            $this->__header .= 'Content-Type: multipart/alternative; ' .
            'boundary="' . $this->__message_boundary . '"' . "\n";
        }
        
        return $this->__header;
    }
    
    /**
     * Return the recipients/to fields.
     *
     * @return string
     **/
    private function get_recipients()
    {
        $ret = array();
        if ($this->to) $ret[] = $this->to;
        if ($this->recipients) {
            if (is_array($this->recipients)) {
                $ret += $this->recipients;
            } else {
                $ret[] = $this->recipients;
            }
        }
        return implode(',', $ret);
    }
    
    /**
     * Creates body of the message and loads it into $__content.
     *
     * @return string
     **/
    public function get_content()
    {
        // intialize content string
        $this->__content = '';
        
        if ($this->is_plain_text()) {
            $this->__content = $this->get_text();
        } else {
            // add text verison to message
            $this->__content .= "--{$this->__message_boundary}\n";
            $this->__content .= "Content-Type: text/plain; charset={$this->charset}\n";
            $this->__content .= "Content-Transfer-Encoding: {$this->__content_transfer_encoding}\n";
            $this->__content .= "Content-Disposition: inline\n\n";
            $this->__content .= $this->get_text();
            $this->__content .= "\n\n";
            
            // add html verison to message
            $this->__content .= "--{$this->__message_boundary}\n";
            $this->__content .= "Content-Type: text/html; charset={$this->charset}\n";
            $this->__content .= "Content-Transfer-Encoding: {$this->__content_transfer_encoding}\n";
            $this->__content .= "Content-Disposition: inline\n\n";
            $this->__content .= $this->get_html();
            $this->__content .= "\n\n--{$this->__message_boundary}--\n";
        }
        
        // get attachments string
        if ($this->has_attachments()) {
            $this->__content .= $this->get_attachments_str();
        }
        
        return $this->__content;
    }
    
    /**
     * Insert the view into the email.
     *
     * @param string $filename Path of file.
     * @return string
     **/
    private function get_include_contents($filename)
    {
        return ActionView::include_contents($filename);
    }
    
    /**
     * Get text version of message and remove all html tags from a string. It
     * also replaces links with a text friendly link.
     *
     * @return string
     **/
    public function get_text()
    {
        $return = $this->text;
        
        if (!$return) {
            $text = $this->get_include_contents(
                VIEWS_PATH . underscore($this->to_string()) . DS .
                $this->_action . '.txt'
                );
            $return = $text ? $text : $this->get_html();
            $return = preg_split('~</head>~', $return);
            
            // remove head
            if (count($return) > 1) {
                $return = $return[1];
            } else {
                $return = $return[0];
            }
            
            // clean empty lines and extra spaces
            $lines = split("\n", $return);
            $return = '';
            foreach ($lines as $line) {
                if (empty($line)) continue;
                $return .= trim($line) . "\n\n";
            }
            
        }
        
        $return = preg_replace(
                    '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\\/a>/i',
                    '$4 ($2)',
                    $return
                    );
        
        return trim(str_replace("\n\n\n", "\n\n", strip_tags($return)));
    }
    
    /**
     * Get html version of message.
     *
     * @return string
     **/
    public function get_html()
    {
        if ($this->html) return $this->html;
        
        $html = $this->get_include_contents(
            VIEWS_PATH . underscore($this->to_string()) . DS .
            $this->_action . '.' . $GLOBALS['CREOVEL']['VIEW_EXTENSION']
            );
        
        // insert html into layout (template) for html verison of the message
        if (!empty($this->layout)) {
            $template_path = VIEWS_PATH . 'layouts' . DS .
                $this->layout . '.' . $GLOBALS['CREOVEL']['VIEW_EXTENSION'];
            $html = str_replace(
                        '@@page_contents@@',
                        $html, 
                        $this->get_include_contents($template_path)
                        );
        }
        
        return $html;
    }
    
    /**
     * Formats email address properties into a string.
     *
     * @param string $email_address
     * @return string
     **/
    public function get_email_address($email_address)
    {
        return is_array($email_address)
            ? implode(',', $email_address)
            : $email_address;
    }
    
    /**
     * Returns email subject.
     *
     * @return string
     **/
    public function get_subject()
    {
        return str_replace("\n", '', $this->subject);
    }
    
    /**
     * Adds a attachment to the email.
     *
     * @param string $file_path Path of the attachment.
     * @param string $content_type Type of content.
     * @param string $content_transfer_encoding Encoding of content.
     * @return string
     **/
    public function add_attachment($file_path, $content_type = null, $content_transfer_encoding = null)
    {
        $key = 'attachment'.count($this->__attachments);
        $file_name = basename($file_path);
        
        $this->__attachments[$key]['content_id'] = $key;
        $this->__attachments[$key]['content_type'] =
            $content_type
            ? $content_type
            : $this->get_content_type($file_name);
        $this->__attachments[$key]['content_transfer_encoding'] =
            $content_transfer_encoding
            ? $content_transfer_encoding
            : $this->get_transfer_encoding($file_name);
        $this->__attachments[$key]['file_name'] = $file_name;
        $this->__attachments[$key]['content_data'] =
            $this->encode_attachment($file_path);
            
        return $key;
    }
    
    /**
     * Returns attachments count or false if none.
     *
     * @return boolean
     **/
    public function has_attachments()
    {
        return count($this->__attachments);
    }
    
    /**
     * Get file content type for attachments.
     *
     * @param string $file_path Path to file.
     * @return string
     **/
    public function get_content_type($file_path)
    {
        return get_mime_type($file_path);
    }
    
    /**
     * Get file transfer encoding for attachments.
     *
     * @return string
     **/
    public function get_transfer_encoding()
    {
        return 'base64';
    }
    
    /**
     * Encode $file_path and return base64 string.
     *
     * @param string $file_path Path to file.
     * @return string
     **/
    public function encode_attachment($file_path)
    {
        if ( file_exists($file_path) ) {
            
            $file = fopen($file_path, 'r');
            $attachment = fread($file, filesize($file_path));
            $attachment = chunk_split(base64_encode($attachment));
            fclose($file);
            
            return $attachment;
        } else {
            return true;
        }
    }
    
    /**
     * Build attachments string for message.
     *
     * @param string $file_path Path to file.
     * @return string
     **/
    public function get_attachments_str()
    {
        if ($this->has_attachments()) {
            $return = "\n--{$this->__message_boundary}--\n";
            foreach ($this->__attachments as $content_id => $attachment) {
                
                $return .= "\n--{$this->mime_boundary}\n";
                $return .= "Content-Type: {$attachment[content_type]}; name={$attachment[file_name]}\n";
                $return .= "Content-Transfer-Encoding: {$attachment[content_transfer_encoding]}\n"; 
                $return .= "Content-ID: {$content_id}\n";
                $return .= "Content-Disposition: attachment; filename=\"{$attachment[file_name]}\"\n\n";
                $return .= $attachment['content_data'];
                $return .= "\n\n";
            }
            return $return;
        } else {
            return false;
        }
    }
} // END class ActionMailer extends ActionController