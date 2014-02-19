<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: pipes.php 141 2014-01-24 10:36:21Z tung $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined('PIPES_CORE') or die('Restricted access');

class PIPESControllerPipes extends Controller
{

    public function __construct()
    {

    }

    function display($cachable = false, $urlparams = false)
    {
        return;
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
        header('Location: ' . $url);
    }

    public function delete()
    {
        $mod = $this->getModel('pipes');
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $res = $mod->delete($id);
        PIPES::add_message($res);
        $url = remove_query_arg(array('id', 'action', 'action2'), $_SERVER['HTTP_REFERER']);
        header('Location: ' . $url);
        exit();
        //$this->display();
    }

    public function copy()
    {
        $mod = $this->getModel('pipes');
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $res = $mod->copy($id);
        PIPES::add_message($res);

        $url = remove_query_arg(array('id', 'action', 'action2'), $_SERVER['HTTP_REFERER']);
        header('Location: ' . $url);
        exit();
    }

    public function update_meta()
    {
        if (isset($_POST['uid'])) {
            $user = $_POST['uid'];
            $value = $_POST['select'];
            update_user_meta($user, 'pipes_help_box', $value);

            return 'Success!';
        }
    }

    public function export_to_share()
    {
        $mod = $this->getModel('pipes');
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $res = $mod->export_to_share($id);
        //PIPES::add_message($res->msg);
        if (count($res->result) == 1) {
            $file_name = sanitize_title($res->result[0]->name) . '.pipe';
        } else {
            $file_name = 'pipes-' . date('d-m-Y', time()) . '.pipe';
        }
        $fp = fopen($file_name, 'w');
        foreach ($res->result as $result) {
            fwrite($fp, json_encode($result) . "\n");
        }
//var_dump(filesize("$file_name"));die;
        fclose($fp);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize("$file_name") . ";");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Transfer-Encoding: binary");

        readfile($file_name);
        /*$url = remove_query_arg(array('id', 'task', 'action', 'action2'), $_SERVER['HTTP_REFERER']);
        header('Location: ' . $url);*/
        exit();
    }

    public function import_from_file()
    {
        $mod = $this->getModel('pipes');
        if (isset ($_FILES["file_import"]["name"])) {
            $filename = $_FILES["file_import"]["name"];
            $file_content = file_get_contents($filename);
            $items = explode("\n", $file_content);
            $new_pipes = array();
            foreach ($items as $value) {
                if ($value != '') {
                    $item = json_decode($value);
                    $new_pipes[] = $mod->import_from_file($item);
                }
            }
            $message = implode("</br>", $new_pipes);
            PIPES::add_message($message);
        }

    }
}