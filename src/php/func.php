<?php

    class AddLead
    {
        
        // ------------------------------------ Определяем переменные ------------------------------------

        public $name;
        public $phone;
        public $email;
        public $city;

		public $formname;
		public $utm_source;
		public $utm_medium;
		public $utm_campaign;
		public $utm_content;
		public $utm_term;
        public $yclid;
        
        public $clientsMsg;
        public $clientsMsgTelegram;

        public $managersMsg;
        
        // ------------------------------------ Конструктор переменных ------------------------------------

        function __construct()
        {

            // Обрабатываем данные с формы

            $this -> name 			= $_POST['name'];
            $this -> phone 			= $_POST['phone'];
            $this -> email 			= $_POST['email'];
            $this -> city 			= $_POST['city'];
    
            $this -> formname		= $_POST['formname'];
            $this -> utm_source		= $_POST['utm_source'];
            $this -> utm_medium		= $_POST['utm_medium'];
            $this -> utm_campaign	= $_POST['utm_campaign'];
            $this -> utm_content	= $_POST['utm_content'];
            $this -> utm_term		= $_POST['utm_term'];
            $this -> yclid			= $_POST['yclid'];

            // Формируем сообщение для Telegram

            $this -> clientsMsgTelegram = "*Имя:* " . $this -> name . "\n";
            $this -> clientsMsgTelegram .= "*Телефон:* " . $this -> phone . "\n";
            $this -> clientsMsgTelegram .= "*Форма:* " . $this -> formname . "\n";

            // Формируем сообщение для клиента на Email

            $this -> clientsMsg     = "<p><strong>Имя:</strong> " . $this -> name . "</p>\r\n";
            $this -> clientsMsg     .= "<p><strong>Телефон:</strong> " . $this -> phone . "</p>\r\n";
            $this -> clientsMsg     .= "<p><strong>Форма:</strong> " . $this -> formname . "</p>\r\n";

            // Формируем сообщение для менеджеров на Email
    
            $this -> managersMsg = $this -> clientsMsg;
            $this -> managersMsg .= "<p><strong>utm_source:</strong> " . $this -> utm_source . "</p>\r\n";
            $this -> managersMsg .= "<p><strong>utm_medium:</strong> " . $this -> utm_medium . "</p>\r\n";
            $this -> managersMsg .= "<p><strong>utm_campaign:</strong> " . $this -> utm_campaign . "</p>\r\n";
            $this -> managersMsg .= "<p><strong>utm_content:</strong> " . $this -> utm_content . "</p>\r\n";
            $this -> managersMsg .= "<p><strong>utm_term:</strong> " . $this -> utm_term . "</p>\r\n";
            $this -> managersMsg .= "<p><strong>yclid:</strong> " . $this -> yclid . "</p>\r\n";
        }

        // ------------------------------------ Отправка на почту ------------------------------------

        public function sendEmail($toSendClients, $toSendmanagers, $subject, $fromName, $fromEmail)
        {
            if (!$this -> phone) {
                exit();
            }
            
            $clientsMsg = $this -> clientsMsg;
            $managersMsg = $this -> managersMsg;

            $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
            $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <" . $fromEmail . ">\r\n";

            if ($toSendmanagers) {
                mail($toSendManagers, "=?UTF-8?B?" . base64_encode($subject) . "?=", $managersMsg, $headers);
            }

            if ($toSendClients) {
                mail($toSendClients, "=?UTF-8?B?" . base64_encode($subject) . "?=", $clientsMsg, $headers);
            }
        }

        // ------------------------------------ Отправка в Telegram ------------------------------------

        public function sendTelegram($botId, $chatId)
        {
            $clientsMsg = $this -> clientsMsgTelegram;

            $ch = curl_init();
            curl_setopt_array(
                $ch,
                array(
                    CURLOPT_URL => 'https://api.telegram.org/bot' . $botId . '/sendMessage',
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_POSTFIELDS => array(
                        'chat_id' => $chatId,
                        'text' => $clientsMsg,
                        'parse_mode' => 'Markdown'
                    ),
                )
            );

            $out = curl_exec($ch);
        }

        // ------------------------------------ Отправка в GetCourse ------------------------------------

        public function sendGetCourse($accountName, $secretKey, $group)
        {
            $user = [];
            $user['user']['email'] = $this -> email;
            $user['user']['phone'] = $this -> phone;
            $user['user']['first_name'] = $this -> name;
            $user['user']['city'] = $this -> city;
            $user['user']['group_name'] = [$group];
            $user['system']['refresh_if_exists'] = 1;
            
            $json = json_encode($user);
            $base64 = base64_encode($json);

            $ch = curl_init();
            curl_setopt_array(
                $ch,
                array(
                    CURLOPT_URL => 'https://' . $accountName . '.getcourse.ru/pl/api/users',
                    CURLOPT_POST => TRUE,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_POSTFIELDS => array(
                        'action' => 'add',
                        'key' => $secretKey,
                        'params' => $base64
                    ),
                )
            );

            $out = curl_exec($ch);
        }

        // ------------------------------------ Отправка в Bitrix24 ------------------------------------

        public function sendBitrix($title, $domain, $id, $webhook, $assigned) {

            $queryUrl = 'https://' . $domain . '/rest/' . $id . '/' . $webhook . '/crm.lead.add.json';
            $queryData = http_build_query(
                array(
                    'fields' => array(
                        'STATUS_ID' => 'NEW',
                        'SOURCE_ID' => 'WEB',
                        'ASSIGNED_BY_ID' => $assigned,
                        'TITLE' => $title, 
                        'NAME' => $this -> name,
                        'PHONE' => array(
                            array(
                                'VALUE' => $this -> phone, 
                                'VALUE_TYPE' => 'WORK'
                            )
                        ),
                        'EMAIL' => array(
                            array(
                                'VALUE' => $this -> email
                            )
                        ),
                        'COMMENTS' => $this -> managersMsg
                    ),
                    'params' => array('REGISTER_SONET_EVENT' => 'Y')
                )
            );

            $ch = curl_init();
            curl_setopt_array(
                $ch,
                array(
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $queryUrl,
                    CURLOPT_POSTFIELDS => $queryData,
                )
            );

            $result = curl_exec($ch);
            curl_close($ch);

        }

    }