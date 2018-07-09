<?php
use PhpOffice\PhpSpreadsheet\Style;

return [
    // general
    'yesNoQuestion' => [
        'Y' => 'Có',
        'N' => 'Không'
    ],
    'recordsDisplay' => [
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
    ],
    'gender' => [
        'M' => 'Nam',
        'F' => 'Nữ',
    ],
    'bloodGroup' => [
        'A' => 'A',
        'B' => 'B',
        'O' => 'O',
        'AB' => 'AB',
    ],
    'relationship' => [
        '1' => 'Cha',
        '2' => 'Mẹ',
        '3' => 'Vợ',
        '4' => 'Chồng',
        '5' => 'Con trai',
        '6' => 'Con gái',
        '7' => 'Anh trai',
        '8' => 'Em trai',
        '9' => 'Chị gái',
        '10' => 'Em gái',
        '11' => 'Bố dượng',
        '12' => 'Mẹ kế',
    ],
    'preferredHand' => [
        '1' => 'Tay phải',
        '2' => 'Tay trái',
        '3' => 'Hai tay',
    ],
    'eduLevel' => [
        '1' => [
            'vn' => 'Tiểu học',
            'jp' => '小学校'
        ],
        '2' => [
            'vn' => 'Trung học cơ sở',
            'jp' => '中学校'
        ],
        '3' => [
            'vn' => 'Trung học phổ thông',
            'jp' => '高校'
        ],
        '4' => [
            'vn' => 'Trung cấp',
            'jp' => '専門高等学校'
        ],
        '5' => [
            'vn' => 'Cao đẳng',
            'jp' => 'カレッジ'
        ],
        '6' => [
            'vn' => 'Đại học',
            'jp' => '大学'
        ],      
    ],
    'maritalStatus' => [
        '1' => 'Độc thân',
        '2' => 'Đã kết hôn',
        '3' => 'Ly thân',
        '4' => 'Ly hôn'
    ],
    'religion' => [
        '1' => 'Phật giáo',
        '2' => 'Thiên Chúa giáo',
        '3' => 'Tin Lành',
        '4' => 'Hòa Hảo',
        '5' => 'Đạo Cơ Đốc',
        '6' => 'Không',
    ],
    'nation' => [
        '1' => 'Kinh',
        '2' => 'Chứt',
        '3' => 'Mường',
        '4' => 'Thổ',
        '5' => 'Bố Y',
        '6' => 'Giáy',
        '7' => 'Lào',
        '8' => 'Lự',
        '9' => 'Nùng',
        '10' => 'Sán Chay',
        '11' => 'Tày',
        '12' => 'Thái',
        '13' => 'Cờ Lao',
        '14' => 'La Chí',
        '15' => 'La Ha',
        '16' => 'Pu Péo',
        '17' => 'Ba Na',
        '18' => 'Brâu',
        '19' => 'Bru - Vân Kiều',
        '20' => 'Chơ Ro',
        '21' => 'Co',
        '22' => 'Cơ Ho',
        '23' => 'Cơ Tu',
        '24' => 'Giẻ Triêng',
        '25' => 'Hrê',
        '26' => 'Kháng',
        '27' => 'Khơ Me',
        '28' => 'Khơ Mú',
        '29' => 'Mạ',
        '30' => 'Mảng',
        '31' => 'M\'Nông',
        '32' => 'Ơ Đu',
        '33' => 'Rơ Măm',
        '34' => 'Tà Ôi',
        '35' => 'Xinh Mun',
        '36' => 'Xơ Đăng',
        '37' => 'X\'Tiêng',
        '38' => 'Dao',
        '39' => 'H\'Mông',
        '40' => 'Pà Thẻn',
        '41' => 'Chăm',
        '42' => 'Chu Ru',
        '43' => 'Ê Đê',
        '44' => 'Gia Rai',
        '45' => 'Ra Glai',
        '46' => 'Hoa',
        '47' => 'Ngái',
        '48' => 'Sán Dìu',
        '49' => 'Cống',
        '50' => 'Hà Nhì',
        '51' => 'La Hủ',
        '52' => 'Lô Lô',
        '53' => 'Phù Lá',
        '54' => 'Si La',
    ],
    'country' => [
        '01' => [
            'vn' => 'Việt Nam',
            'jp' => 'ベトナム'
        ],
        '02' => [
            'vn' => 'Nhật Bản',
            'jp' => '日本'
        ]
    ],
    'cardType' => [
        '1' => 'CMND',
        '2' => 'PASSPORT',
        '3' => 'VISA',
    ],
    'language' => [
        '1' => [
            'vn' => 'Tiếng Nhật',
            'jp' => '英語',
        ],
        '2' => [
            'vn' => 'Tiếng Anh',
            'jp' => '日本語',
        ]
    ],
    'noAvatar' => 'no_avatar.png',

    // order
    'interviewType' => [
        '1' => 'Skype',
        '2' => 'Trực tiếp',
    ],
    'interviewStatus' => [
        '1' => 'Chưa phỏng vấn',
        '2' => 'Đang phỏng vấn',
        '3' => 'Đã phỏng vấn',
        '4' => 'Đã đóng'
    ],
    'interviewResult' => [
        '0' => '-',
        '1' => 'Pass',
        '2' => 'Fail',
    ],
    'workTime' => [
        '3' => '3 năm',
        '5' => '5 năm'
    ],

    //education
    'lessons' => [
        '0' => 'Bảng chữ cái',
        '1' => 'Bài 1',
        '2' => 'Bài 2',
        '3' => 'Bài 3',
        '4' => 'Bài 4',
        '5' => 'Bài 5',
        '6' => 'Bài 6',
        '7' => 'Bài 7',
        '8' => 'Bài 8',
        '9' => 'Bài 9',
        '10' => 'Bài 10',
        '11' => 'Bài 11',
        '12' => 'Bài 12',
        '13' => 'Bài 13',
        '14' => 'Bài 14',
        '15' => 'Bài 15',
        '16' => 'Bài 16',
        '17' => 'Bài 17',
        '18' => 'Bài 18',
        '19' => 'Bài 19',
        '20' => 'Bài 20',
        '21' => 'Bài 21',
        '22' => 'Bài 22',
        '23' => 'Bài 23',
        '24' => 'Bài 24',
        '25' => 'Bài 25',
        '26' => 'Bài 26',
        '27' => 'Bài 27',
        '28' => 'Bài 28',
        '29' => 'Bài 29',
        '30' => 'Bài 30',
        '31' => 'Bài 31',
        '32' => 'Bài 32',
        '33' => 'Bài 33',
        '34' => 'Bài 34',
        '35' => 'Bài 35',
        '36' => 'Bài 36',
        '37' => 'Bài 37',
        '38' => 'Bài 38',
        '39' => 'Bài 39',
        '40' => 'Bài 40',
        '41' => 'Bài 41',
        '42' => 'Bài 42',
        '43' => 'Bài 43',
        '44' => 'Bài 44',
        '45' => 'Bài 45',
        '46' => 'Bài 46',
        '47' => 'Bài 47',
        '48' => 'Bài 48',
        '49' => 'Bài 49',
        '50' => 'Bài 50',
    ],
    //document
    'document' => [
        '1' => [
            'type' => 'Bằng cấp 2/cấp 3/Trung cấp/Cao đẳng/Đại học (Nộp cả bằng gốc)',
            'quantity' => '3 bản'
        ],
        '2' => [
            'type' => 'Chứng minh nhân dân/Thẻ căn cước',
            'quantity' => '6 bản'
        ],
        '3' => [
            'type' => 'Hộ chiếu (Passport) _ Nộp cả hộ chiếu gốc',
            'quantity' => '3 bản'
        ],
        '4' => [
            'type' => 'Số hộ khẩu (Photo công chứng)',
            'quantity' => '2 bản'
        ],
        '5' => [
            'type' => 'Giấy khai sinh (Bản sao)',
            'quantity' => '4 bản'
        ],
        '6' => [
            'type' => 'Sơ yếu lý lịch (Mẫu Công ty)',
            'quantity' => '1 bản'
        ],
        '7' => [
            'type' => 'Đơn xác nhận phụng dưỡng (Mẫu Công ty)',
            'quantity' => '3 bản'
        ],
        '8' => [
            'type' => 'Giấy đăng ký kết hôn (nếu có)',
            'quantity' => '3 bản'
        ],
        '9' => [
            'type' => 'Chứng minh nhân dân photo công chứng của những người trong đơn phụng dưỡng',
            'quantity' => '3 bản/người'
        ],
        '10' => [
            'type' => 'Hình 4 x 6 (Áo trắng, nền trắng)',
            'quantity' => '15 tấm'
        ],
        '11' => [
            'type' => 'Hình 3 x 4 (Áo trắng, nền trắng)',
            'quantity' => '15 tấm'
        ],
        '12' => [
            'type' => 'Hình 4,5 x 4,5 (Áo trắng, nền trắng)',
            'quantity' => '2 tấm'
        ],
    ],
    // user
    'scope' => [
        'Users' => 'Users scope',
        'Students' => 'Students scope',
        'Orders' => 'Orders scope'
    ],
    'permission' => [
        '0' => 'Full access',
        '1' => 'Read only'
    ],
    'passwordDefault' => '123456789',

    // address vn
    'addressType' => [
        '1' => 'household',
        '2' => 'currentAddress'
    ],

    'addressENLevel' => [
        'Thành phố' => 'City',
        'Tỉnh' => 'Province',
        'Quận' => 'District',
        'Huyện' => 'District',
        'Phường' => 'Ward',
        'Xã' => 'Commune',
        'Thị trấn' => 'Town'
    ],

    // address jp
    'cityJP' => [
        '1' => [
            'rmj' => 'TOKYO',
            'kj' => 'TOKYO'
        ],
        '2' => [
            'rmj' => 'OSAKA',
            'kj' => 'OSAKA'
        ]
    ],
    // student
    'studentStatus' => [
        '1' => 'Lịch hẹn',
        '2' => 'Học viên',
        '3' => 'Đã xuất cảnh',
        '4' => 'Về nước',
        '5' => 'Bỏ trốn',
        '6' => 'Rút hồ sơ',
    ],
    'studentSubject' => [
        '1' => 'Bộ đội xuất ngũ',
        '2' => 'Đối tượng chính sách',
        '3' => 'Dân tộc thiểu số',
    ],

    // event
    'eventScope' => [
        '1' => 'Only me',
        '2' => 'Global'
    ],

    // .docx template
    'studentCodeTemplate' => 'LD-:date-:counter',
    'blackListTemplate' => 'Blacklist user :username, for :error',
    'currentAddressTemplate' => ':ward, :district, :city',
    'schoolTemplate' => ":schoolNameEN:eduLevelJP校卒業\nTốt nghiệp trường :eduLevelVN :schoolNameVN",
    'folderImgTemplate' =>  ROOT . DS . 'webroot' . DS . 'img' . DS . 'templates',
    'resume' => [
        'filename' => '履歴書-:firstName.docx'
    ],

    // .xlsx template
    'headerStyle' => [
        'font' => [
            'bold' => true,
            'color' => [
                'rgb' => 'FFFFFF'
            ]
        ],
        'alignment' => [
            'horizontal' => Style\Alignment::HORIZONTAL_CENTER
        ],
        'fill' => [
            'fillType' => Style\Fill::FILL_SOLID,
            'color' => [
                'rgb' => '3c8dbc'
            ]
        ]
    ],
    'tableStyle' => [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Style\Border::BORDER_THIN,
                'color' => [
                    'rgb' => '3c8dbc'
                ]
            ]
        ]
    ]
];