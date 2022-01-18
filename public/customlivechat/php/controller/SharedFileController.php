<?php

class SharedFileController extends Controller
{
    public function downloadAction()
    {
        $request = $this->get('request');
        $id      = $request->getVar('id');
//        var_dump($request);die;
        $file    = SharedFileModel::repo()->find($id);
//        var_dump($file);die;

        if(!$file)
        {
            return $this->text('File not found');
        }

        // Check permissions to download

        $user = $this->get('user');

        if(!$user->hasSomeRoles(array('OPERATOR', 'ADMIN')))
        {
            $guest = $this->get('guest');

            $upload  = UploadModel::repo()->find($file->upload_id);
            $message = MessageModel::repo()->find($upload->message_id);

            if($guest->getTalkId() !== $message->talk_id)
            {
                return $this->text('Access denied');
            }
        }

        // Decode and send

        $file->sendDecoded();

        if(strstr($file->type,"image")){die;}

//        var_dump($this->download('', $file->original_name, $file->type));die;
        return $this->download('', $file->original_name, $file->type);
    }
}

?>
