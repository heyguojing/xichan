<?php
class PublicAction extends Action {
	public function verify() {
        $w = isset($_GET['w']) ? (int) $_GET['w'] : 50;
        $h = isset($_GET['h']) ? (int) $_GET['h'] : 30;
        import("ORG.Util.Image");
        ob_end_clean();
        Image::buildImageVerify(4, 1, 'png', $w, $h);
    }

}