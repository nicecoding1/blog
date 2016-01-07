<?php

$mode = $_REQUEST['mode'];
$address = $_REQUEST['email'];

function verify_email($address, &$error) {
	$G5_SERVER_TIME = time();

	$WAIT_SECOND = 3; // ?초 기다림

	list($user, $domain) = explode("@", $address);


	// 도메인에 메일 교환기가 존재하는지 검사
	if (checkdnsrr($domain, "MX")) {
		// 메일 교환기 레코드들을 얻는다
		if (!getmxrr($domain, $mxhost, $mxweight)) {
			$error = '메일 교환기를 회수할 수 없음';
			return false;
		} else {
			$_cnt = count($mxhost);
			if($_cnt == 0) {
				$mxhost[] = $domain;
				$mxweight[] = 1;
			}
		}
	} else {
		// 메일 교환기가 없으면, 도메인 자체가 편지를 받는 것으로 간주
		$mxhost[] = $domain;
		$mxweight[] = 1;
	}

	// 메일 교환기 호스트의 배열을 만든다.
	for ($i=0; $i<count($mxhost); $i++)
		$weighted_host[$i] = $mxhost[$i];
	//@ksort($weighted_host);

	// 각 호스트를 검사
	foreach($weighted_host as $host) {
		// 호스트의 SMTP 포트에 연결
		if (!($fp = @fsockopen($host, 25))) continue;

		// 220 메세지들은 건너뜀
		// 3초가 지나도 응답이 없으면 포기
		socket_set_blocking($fp, false);
		$stoptime = $G5_SERVER_TIME + $WAIT_SECOND;
		$gotresponse = false;

		while (true) {
			// 메일서버로부터 한줄 얻음
			$line = fgets($fp, 1024);

			if (substr($line, 0, 3) == '220') {
				// 타이머를 초기화
				$stoptime = $G5_SERVER_TIME + $WAIT_SECOND;
				$gotresponse = true;
			} else if ($line == '' && $gotresponse)
				break;
			else if ($G5_SERVER_TIME > $stoptime)
				break;
		}

		// 이 호스트는 응답이 없음. 다음 호스트로 넘어간다
		if (!$gotresponse) continue;

		socket_set_blocking($fp, true);

		// SMTP 서버와의 대화를 시작
		fputs($fp, "HELO {$_SERVER['SERVER_NAME']}\r\n");
		echo "HELO {$_SERVER['SERVER_NAME']}\r\n";
		fgets($fp, 1024);

		// From을 설정
		fputs($fp, "MAIL FROM: <info@$domain>\r\n");
		//echo "MAIL FROM: <info@$domain>\r\n";
		fgets($fp, 1024);

		// 주소를 시도
		fputs($fp, "RCPT TO: <$address>\r\n");
		//echo "RCPT TO: <$address>\r\n";
		$line = fgets($fp, 1024);

		// 연결을 닫음
		fputs($fp, "QUIT\r\n");
		fclose($fp);

		if (substr($line, 0, 3) != '250') {
			// SMTP 서버가 이 주소를 인식하지 못하므로 잘못된 주소임
			$error = $line;
			return false;
		} else
			// 주소를 인식했음
			return true;
	}

	$error = '메일 교환기에 도달하지 못하였습니다.';
	return false;
}

if($mode == "verify") {
	$ret = verify_email($address, &$error);
	echo "<meta charset=\"euc-kr\">";
	if($ret) echo "<script>alert('이메일주소 검사 성공');</script>";
	else echo "<script>alert('이메일주소 검사 실패\\n\\n$error');</script>";
	echo "<script>location.href='verify_email.php';</script>";
	exit;
}

?>

<meta charset="euc-kr">
<title>이메일주소 검사 프로그램</title>
<form method="post">
<input type="hidden" name="mode" value="verify">
이메일주소 검사 프로그램<p>
이메일주소 <input type="text" name="email" size="20" maxlength="40" required autofocus> 
<input type="submit" value="실행">
</form>
