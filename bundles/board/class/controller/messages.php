<?

namespace Infuso\Board\Controller;

class Messages extends \Infuso\Core\Controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Экшн получения списка сообщений
     **/             
    public static function post_list($p) {

		$ret = array();
		$user = user::active();
		$emails = user_mail::all()->eq("userID",$user->id());

        $lastDate = null;
		
		foreach($emails as $email) {

            $date = $email->pdata("sent")->date()->text();
            if($date!=$lastDate) {

                $ret["data"][] = array(
                    "date" => $date,
                );
                $lastDate = $date;
            }

		    $ret["data"][] = array(
				"text" => $email->subject(),
                "time" => date("H:i",$email->pdata("sent")->stamp()),
                "taskID" => $email->mailer()->param("taskID"),
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
