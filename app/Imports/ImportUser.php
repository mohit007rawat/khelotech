<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserImages;
use App\Models\UsersImage;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class ImportUser implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $spreadsheet = IOFactory::load(request()->file('file'));
        $i=0;
        foreach ($spreadsheet->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();
                switch ($drawing->getMimeType()) {
                    case MemoryDrawing::MIMETYPE_PNG :
                        $extension = 'png';
                        break;
                    case MemoryDrawing::MIMETYPE_GIF:
                        $extension = 'gif';
                        break;
                    case MemoryDrawing::MIMETYPE_JPEG :
                        $extension = 'jpg';
                        break;
                }
            } else {
                $zipReader = fopen($drawing->getPath(), 'r');
                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader, 1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
            }
            
            $myFileName = 'users/'.time() .++$i. '.' . $extension;
            file_put_contents('storage/' . $myFileName, $imageContents);
            $temp[]  = $myFileName;
        }
        
        foreach($rows as $key=>$row){
            $user = User::create([
                "name" => $row['first_name'],
                "middle_name" => $row['middle_name'],
                "last_name" => $row['last_name'],
                "mobile_number" => $row['mobile_number'],
                "email" => $row['mobile_number'].'@gmail.com',
                "state" => $row['select_state'],
                "city" => $row['select_city'],
                "address" => $row['address'],
                "profile_photo" => $temp[$key],     
                "role_id" => 2,
                "password" => bcrypt($row['mobile_number']),
            ]);
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'mobile_number' => ['required'],
            'select_state' => ['required'],
            'select_city' => ['required'],
            'address' => ['required'],
        ];
    }
}
