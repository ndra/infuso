<?

namespace Infuso\Board\Controller;

class Profile extends \Infuso\Core\Controller {

    public function postTest() {
        return user::active()->exists();
    }

    public function post_getProfile() {

        $user = user::active();

        return array(
            "nickName" => $user->nickName(),
        );

    }

    public function post_saveProfile($data) {

        $user = user::active();

        $user->data("nickName",$data["data"]["nickName"]);

    }

    public function post_getUserpick() {
        $user = user::active();
        return array(
            "x200" => $user->userpick()->preview(200,200)->crop(),
        );
    }

    public function post_saveUserpick($p) {
        $files = mod::app()->files();
        $file = $files["file"]["tmp_name"];
        $user = user::active();
        $userpick = $user->storage()->addUploaded($file,"userpick");
        $user->data("userpick",$userpick);
    }

}
