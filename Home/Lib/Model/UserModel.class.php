<?php 
	class UserModel extends Model{
		public function regist(){

		}

		protected $_validate = array(
			// array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
			array('nickname','require');
		);
	}