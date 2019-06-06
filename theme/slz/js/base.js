/*updated 20131216*/
/*call*/
//

$.fn.conttabbox = function() {
	var $object = this;
	var $tabs = $object.find(".tabs .tab");
	var $cnts = $object.find(".cnts .cnt");

	$tabs.click(function(event) {
		var i = $tabs.index(this);
		$cnts.hide();
		$cnts.eq(i).show();

		$tabs.removeClass('on');
		$(this).addClass('on');

		return false;
	});
	$tabs.first().click();
}

$.fn.placeholder = function() {
	var $object = this;
	var t = $object.attr("value");
	if (t == "") {
		return;
	}
	$object.data("t", t);

	$object.focus(function(event) {
		$object.val("");
	});
	$object.blur(function(event) {
		if ($object.val() == "") {
			$object.val($object.data("t"));
		}
	});
}