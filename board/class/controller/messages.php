<?

class board_controller_messages extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Экшн получения списка сообщение
     **/             
    public static function post_list($p) {

		$ret = array();
		$user = user::active();
		$emails = user_mail::all()->eq("userID",$user->id());
		
		foreach($emails as $email) {
		    $ret["data"][] = array(
				"text" => $email->subject(),
			);
		}
		
		$user->extra("board/messagesRead",util::now());
		
		$emails->eq("read",0)->data("read",1);
		
		return $ret;
       
    }
    
    public static function post_getUnreadMessagesNumber() {

		$ret = array();
		$user = user::active();
		$emails = user_mail::all()
			->eq("userID",$user->id())
			->eq("read",0);
		return $emails->count();

    }

}
