<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AWS;
use View;

class SMSController extends Controller
{
    protected $templates = array (
        'default',
        'btnoff',
        'btnon',
        'btnredirect',
        'missedwo',
        'nineoneone',
        'offhookto',
        'pmsca'
    );

    protected $template;

    /*
     *
     * @param json string
     *   json consists of a key which indicates the type, and then
     *   an object that includes the variables for the template
     * @return json string with web status 200 or 501
     *
     *
     */

    public function sendSMS($json){
        $data = json_decode(urldecode($json), true);
        $type = key($data);
        $info_array = array();
        $to = "";
        foreach ($data[$type] as $key => $info) {
            if (strtolower($key) == "to") {
                $to = (string) $info;
            } else {
                $info_array[strtolower($key)] = $info;
            }
        }
        // verify format of phone number
        $to = $this->phoneFormat($to);
        if (!$to) {
            return  response()->json(array('failure' => true, 'error' => "invalid phone number"), 501);
        }

        $this->template = strtolower(key($data));
        if (!in_array($this->template, $this->templates)) {
            $this->template = 'default';
        }

        if ($this->template == 'default') {
            $data = array();
            //text should come in as 'body' but we'll be safe
            if (isset($info_array['body'])) {
                $data['data'] = $info_array['body'];
            }
            if (isset($info_array['text'])) {
                $data['data'] = $info_array['text'];
            }
            if (isset($info_array['message'])) {
                $data['data'] = $info_array['message'];
            }
            //if there still isn't a body, then we'll just take what is in the other properties
            if (empty($data)) {
                $data['data'] = "";
                foreach ($info_array as $prop => $info_part) {
                   $data['data'] .= " $prop: $info_part ";
                }
            }
            $info_array['data'] = $data;
        }

        $view = View::make($this->template, $info_array);
        $text = $view->render();

        $sms = AWS::createClient('sns');

        if ($sms->publish([
            'Message' => $text,
            'PhoneNumber' => $to,
            'MessageAttributes' => [
                'AWS.SNS.SMS.SMSType'  => [
                    'DataType'    => 'String',
                    'StringValue' => 'Transactional',
                ]
            ],
        ])) {
            return response()->json(array('success' => true, 'to' => $to), 200);
        } else {
            return  response()->json(array('failure' => true, 'error' => "sending message failed"), 501);
        }
    }

    public function post(Request $request) {
        $data = $request->all();

        foreach ($data as $param => $info) {
            if (json_decode($info) !== NULL) {
                return $this->sendSMS($info);
            } else {
                return  response()->json(array('failure' => true), 501);
            }
        }

    }

    private function phoneFormat($phoneNumber) {
        $phoneNumber = (string)$phoneNumber;
        if (strlen($phoneNumber) == 11 && substr($phoneNumber, 0, 2) == '+1') {
            return $phoneNumber;
        }
        if (strlen($phoneNumber) != 11 ) {
            if (strlen($phoneNumber) == 10 &&  substr($phoneNumber, 0, 1) != '1') {
                return "+1" . $phoneNumber;
            } else if (strlen($phoneNumber) == 11 && substr($phoneNumber, 0, 1) == '1') {
                return "+" . $phoneNumber;
            } else {
                return false;
            }
        }
    }


}
