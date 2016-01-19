<script>
$(document).ready(function(){
	var dt = new Date();
	var y = dt.getFullYear();
	var m = dt.getMonth()+1;
	var d = dt.getDate()+1;
	var h = dt.getHours();

	if(h >= 15) d++;
	mindt = y+"-"+m+"-"+d;

	$("#outdt").datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat:"yy-mm-dd",
		showMonthAfterYear:true,
		dayNamesMin: ['일','월','화','수','목','금','토'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		minDate: mindt,
		maxDate: "+10D",
		onSelect: function(value) {
			var dt2 = new Date(value);
			var w = dt2.getDay();
			if(w == 6 || w == 0) {
				alert('토요일, 일요일에는 퇴사를 할 수 없습니다.');
			}
		},
		onClose: function(value) {
			var dt2 = new Date(value);
			var w = dt2.getDay();
			if(w == 6 || w == 0) {
				$("#outdt").datepicker("setDate", "");
			}

		}
	});
});
</script>
