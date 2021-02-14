<?php
/*
오늘은 아주 간단하면서도 유용한 팁을 하나 드리려고 합니다.
문자 내용 중에 휴대폰번호를 추출하는 것인데요.
preg_match_all() 함수를 사용하면 아주 쉽게 할 수 있습니다.
정규식 패턴만 변경하면 다양한 곳에 적용해서 사용할 수 있답니다.

[문자내용]
[MMS]SKT>번호변경안내 메시지보내신
01012345678번은
01012345679번으로 변경되었습니다.
*/

$str = "[MMS]SKT>번호변경안내 메시지보내신
01012345678번은
01012345679번으로 변경되었습니다.";

$pattern = "/(\d)+/";
preg_match_all($pattern, $str, $matches);
print_r($matches[0]);
?>
