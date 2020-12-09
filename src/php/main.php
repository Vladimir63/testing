<?php

	// Отключаем ошибки
	ini_set('display_errors','On');

	// Подключаем файл с функциями
	include('func.php');
	
	// Создаем объект класса AddLead
	$lead = new AddLead();

	// Вызываем методы
	// $lead -> sendTelegram('1343619438:AAFG2-tWFaRyh6g2LtTPjVsM8D2Tge8NnwY', '-1001255265205');
