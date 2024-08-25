<?php

namespace App\Controllers;
use DirectoryIterator;
use App\Models\ToolModel;
use CodeIgniter\Files\File;

class Tool extends BaseController
{
    public function index() {
        return view('tool');
    }

    public function processFile() {
        $origin_path = WRITEPATH . 'uploads\\convert\\original';
        $renamed_path = WRITEPATH . 'uploads\\convert\\renamed';
        $original_files = new DirectoryIterator($origin_path);

        //Lấy ds tên ảnh-mã sv trong db
        // $toolModel = model('ToolModel');
        // $name_msv = $toolModel->getDS();

        // $count_db = count($name_msv);
        $count_total = 0;
        $count_renamed = 0;

        foreach ($original_files as $file) {
            if (!$file->isDot()) {
                $count_total++;
                //Lấy array tên và ext của file
                $name_extension = pathinfo($file->getFilename());
                $name = $name_extension['filename'];

                $msv = '24A1501D' . trim($name);
                //Tạo object File, truyền vào absolute path
                $CI_file = new File($origin_path."\\".$file);
                //Đổi tên file và move vào thư mục mới. File cũ sẽ bị xóa.
                $CI_file->move($renamed_path, $msv.'.'.$name_extension['extension']);
                $count_renamed++;
                //Kiểm tra xem file name có ứng với msv nào không
                // if (isset($name_msv[$name])) {
                // }
            }
        }
        echo "Đã đổi tên {$count_renamed} ảnh trong tổng số {$count_total} ảnh.";
    }
}