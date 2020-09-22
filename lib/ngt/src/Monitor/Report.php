<?php

namespace NGT\Monitor;

use NGT\Registry;
use NGT\Monitor\Contracts\Constants;
use Shuchkin\SimpleMail;

class Report
{
    private $mail;
    private $html = '';
    private $context = '';
    private $results;

    public function __construct(string $context)
    {
        $this->context = $context;
        $this->results = Registry::storage();

        $this->mail = new SimpleMail($_ENV['MAIL_DRVR'], [
            'host' => $_ENV['MAIL_HOST'],
            'port'     => $_ENV['MAIL_PORT'],
            'username' => $_ENV['MAIL_USER'],
            'password' => $_ENV['MAIL_PASS']
        ]);

        $this->html = $this->build();
    }

    protected function build() : string
    {
        $html = $this->buildHtmlHeader();

        // foreach website...
        foreach ($this->results as $url => $status) {
            $html.= $this->buildHtmlReport($url, $status); // detail report
        }

        return $html.'</body></html>';
    }

    public function send() : ?bool
    {
        try {
            return $this->mail
                ->setFrom($_ENV['MAIL_FROM'])
                ->setTo($_ENV['MAIL_TO'])
                ->setSubject($_ENV['MAIL_SUBJ'])
                ->setText('Please view this mail as HTML!')
                ->setHTML($this->getHtml(), true)
                ->send();
        } catch (Exception $e) {
            //
            // @TODO Exception Handler
            //
            echo $e->getMessage();
            die('_____Could not send email______');
        }
    }

    private function buildHtmlReport($url, $status) : string
    {
        $html = '<section><header>';
        $html.= $this->buildHtmlStats($status);
        $html.= '<h1>'.$url.'</h1>';

        $html.= '';
        $html.= '</header><ul>';

        // get all status message and build html
        foreach ($status->get() as $k => $msg) {
            $html.= $this->buildMessage($msg);
        }

        return $html.'</ul></section>';
    }

    private function buildMessage(array $msg) : string
    {
        // if we have a context, add eyecatcher to the info
        if ($msg['context'] != '') {
            $html = '<li class="'.$msg['context'].'">';
            $html.= $this->getHtmlButton($msg['context']);
        } else {
            $html = '<li>';
        }

        // add message
        $html.= '<b>'.$msg['class'].'</b>: '.$msg['msg'];

        // if we have a value, add it
        if (isset($msg['value'])) {
            if (is_array($msg['value'])) {
                $html.= ' <span class="value">['.implode(',', $msg['value']).']</span>';
            } else {
                if (isset($msg['value']) && $msg['value'] != "") {
                    $html.= ' <span class="value">['.$msg['value'].']</span>';
                }
            }
        }

        return $html.'</li>';
    }

    private function buildHtmlStats($status) : string
    {
        $html = '<div class="stats">';
        foreach ($status->stats() as $context => $sum) {
            $html.= $this->getHtmlButton($context, (int) $sum);
        }
        return $html.'</div><hr clear="right">';
    }

    private function buildHtmlHeader() : string
    {
        return '<!doctype html>
        <html>
          <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>'.Constants::PROPAGANDA.'</title>
            '.$this->getHtmlStyles().'
          </head>
          <body>';
    }

    private function getHtmlButton(string $context, $sum = null) : string
    {
        $css = '';
        // $css = (! is_null($sum) && ($sum == 0)) ? ' is-null' : '';

        if (! is_null($sum)) {
            $sum = '&nbsp;<b>'.$sum.'</b>&nbsp;&nbsp;';
        }

        switch ($context) {
            case 'info':
                return '<span class="note info'.$css.'">'.$sum.'Info</span>&nbsp;&nbsp;';
            case 'error':
                return '<span class="note error'.$css.'">'.$sum.'error</span>&nbsp;&nbsp;';
            case 'success':
                return '<span class="note success'.$css.'">'.$sum.'Success</span>&nbsp;&nbsp;';
            case 'warning':
                return '<span class="note warning'.$css.'">'.$sum.'Warning</span>&nbsp;&nbsp;';
            default:
                return '';
        }
    }

    private function getHtmlStyles() : string
    {
        return '<style>
            body {
                font-family: Arial, sans-serif, serif;
                background-color: #f6f6f6;
                -webkit-font-smoothing: antialiased;
                font-size: 14px;
                line-height: 1.4;
                margin: 0;
                padding: 1em;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }
            section {
                padding:1em;
                margin-bottom:0.2em;
                background-color:white;
            }
            h1 {
                margin:0;
                /*border-bottom:1px solid #222;*/
            }
            .stats {
                float:right;
            }
            ul {
                font-size:14px;
                list-style-type: none;
                padding-left: 0.5em;
            }
            li {
                padding: .3em;
                margin: 0 0 1px 0;
                /*border-bottom: 1px dotted #efefef;*/
            }
            .value {
                color:#006ccc;
                font-weight:700;
            }
            span.note {
                font-size:10px;
                font-weight:700;
                text-transform: uppercase;
                padding:0.4em;
                background-color:#ccc;
                -webkit-box-shadow: 1px 1px 1px 1px #ccc;
                box-shadow: 1px 1px 1px 1px #ccc;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
            }
            span.note b {
                font-size:12px;
            }
            span.info,
            li.info span.note {
                color:white;
                background-color:blue;
            }
            span.error,
            li.error span.note {
                color:white;
                background-color:#e20707d1;
            }
            span.success,
            li.success span.note {
                color:white;
                background-color:#039a03ba;
            }
            span.warning,
            li.warning span.note {
                color:white;
                background-color:orange;
            }
            span.is-null {
                color:#ccc;
                background-color:#efefef!important;
            }
        </style>
        ';
    }

    public function getHtml() : string
    {
        return $this->html;
    }

    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }
}
