<?php
use PhpOffice\PhpSpreadsheet\Style;

return [
    // general
    'yesNoQuestion' => [
        'Y' => 'Có',
        'N' => 'Không'
    ],
    'yesNoJP' => [
        'Y' => '有',
        'N' => '無'
    ],
    'physResult' => [
        1 => 'Đạt',
        2 => 'Tái khám',
        3 => 'Không đạt',
        4 => 'Chờ kết quả'
    ],
    'dayOffType' => [
        1 => 'Nghỉ lễ',
        2 => 'Nghỉ bù'
    ],
    'smokedrink' => [
        'Y' => [
            'vn' => 'Có',
            'jp' => '有',
        ],
        'L' => [
            'vn' => 'Ít',
            'jp' => ' 有(少)',
        ],
        'N' => [
            'vn' => 'Không',
            'jp' => '無'
        ]
    ],
    'depositType' => [
        1 => 'Thường',
        2 => 'Trọn gói',
        3 => 'Khác',
        4 => 'Chưa có thông tin'
    ],
    'financeStatus' => [
        1 => 'Đã đóng đủ',
        2 => 'Chưa đóng',
        3 => 'Đóng thiếu',
        4 => 'Miễn đóng',
        5 => 'Chưa có thông tin'
    ],
    'recordsDisplay' => [
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
    ],
    'defaultDisplay' => 25,
    'gender' => [
        'M' => 'Nam',
        'F' => 'Nữ',
    ],
    'genderJP' => [
        'M' => '男',
        'F' => '女'
    ],
    'bank' => [
        '1' => 'Ngân hàng Á Châu - ABC',
        '2' => 'Ngân hàng Tiên Phong - TPBank',
        '3' => 'Ngân hàng Đông Á - DongABank',
        '4' => 'Ngân hàng Đông Nam Á - SeABank',
        '5' => 'Ngân hàng An Bình - AnBinhBank',
        '6' => 'Ngân hàng Bắc Á - BacABank',
        '7' => 'Ngân hàng Bản Việt - VietCapitalBank',
        '8' => 'Ngân hàng Hàng Hải Việt Nam - Maritime Bank',
        '9' => 'Ngân hàng Kỹ Thương Việt Nam - Techcombank',
        '10' => 'Ngân hàng Kiên Long - KienLong Bank',
        '11' => 'Ngân hàng Nam Á - NamABank',
        '12' => 'Ngân hàng Quốc Dân - NCB',
        '13' => 'Ngân hàng Việt Nam Thịnh Vượng - VPBank',
        '14' => 'Ngân hàng Phát triển nhà Thành phố Hồ Chí Minh - HDBank',
        '15' => 'Ngân hàng Phương Đông - OCB',
        '16' => 'Ngân hàng Quân đội - Military Bank',
        '17' => 'Ngân hàng Quốc tế - VIB Bank',
        '18' => 'Ngân hàng Sài Gòn - SCBank',
        '19' => 'Ngân hàng TMCP Thương mại Sài Gòn - PVcom Bank',
        '20' => 'Sài Gòn Công Thương - Saigon Bank',
        '21' => 'Ngân hàng Đại chúng - SHB',
        '22' => 'Sài Gòn Thương Tín - Sacombank',
        '23' => 'Ngân hàng Việt Á - VietA Bank',
        '24' => 'Ngân hàng Bảo Việt - BaoViet Bank',
        '25' => 'Việt Nam Thương Tín - Viet Bank',
        '26' => 'Xăng dầu Petrolimex - Petrolimex Group Bank',
        '27' => 'Ngân hàng Xuất Nhập khẩu Việt Nam - Eximbank',
        '28' => 'Bưu điện Liên Việt - LienVietPostBank (LPB)',
        '29' => 'Ngân hàng Ngoại thương Việt Nam - Vietcombank',
        '30' => 'Ngân hàng Công Thương Việt Nam - VietinBank',
        '31' => 'Ngân hàng Đầu tư và Phát triển Việt Nam - BIDV',
        '32' => 'HSBC',
        '33' => 'Standard Chartered',
        '34' => 'Shinhan Bank',
        '35' => 'Ngân hàng Đại Dương - OceanBank',
        '36' => 'Ngân hàng Dầu Khí Toàn Cầu - GPBank',
        '37' => 'Ngân hàng Nông nghiệp và Phát triển nông thôn - Agribank',
        '38' => 'Ngân hàng Xây dựng - CB',
        '39' => 'Ngân hàng Citibank Việt Nam',
        '40' => 'MUFG',
    ],
    'bloodGroup' => [
        'A' => 'A',
        'B' => 'B',
        'O' => 'O',
        'AB' => 'AB',
    ],
    'relationship' => [
        '1' => [
            'vn' => 'Cha',
            'jp' => '父',
        ],
        '2' => [
            'vn' => 'Mẹ',
            'jp' => '母',
        ],
        '3' => [
            'vn' => 'Vợ',
            'jp' => '妻',
        ],
        '4' => [
            'vn' => 'Chồng',
            'jp' => '夫',
        ],
        '5' => [
            'vn' => 'Con trai',
            'jp' => '子',
        ],
        '6' => [
            'vn' => 'Con gái',
            'jp' => '子',
        ],
        '7' =>  [
            'vn' => 'Anh trai',
            'jp' => '兄',
        ],
        '8' => [
            'vn' => 'Em trai',
            'jp' => '弟',
        ],
        '9' => [
            'vn' => 'Chị gái',
            'jp' => '姉',
        ],
        '10' => [
            'vn' => 'Em gái',
            'jp' => '妹',
        ],
    ],
    'preferredHand' => [
        '1' => 'Tay phải',
        '2' => 'Tay trái',
    ],
    'candidateSource' => [
        '1' => 'Facebook',
        '2' => 'Google',
        '3' => 'Điện thoại'
    ],
    'candidateStatus' => [
        '1' => 'Chưa tư vấn',
        '2' => 'Đã tư vấn',
        '3' => 'Tiềm năng',
        '4' => 'Đã kí kết'
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
            'jp' => '高等学校'
        ],
        '4' => [
            'vn' => 'Trung cấp',
            'jp' => '専門学校'
        ],
        '5' => [
            'vn' => 'Cao đẳng',
            'jp' => '短期大学'
        ],
        '6' => [
            'vn' => 'Đại học',
            'jp' => '大学'
        ],
        '7' => [
            'vn' => 'Trung tâm',
            'jp' => 'センター'
        ],
        '99' => [
            'vn' => 'Chưa có thông tin',
            'jp' => '確認中'
        ]
    ],
    'maritalStatus' => [
        '1' => [
            'vn' => 'Độc thân',
            'jp' => '未婚'
        ],
        '2' => [
            'vn' => 'Đã kết hôn',
            'jp' => '既婚'
        ],
        '3' => [
            'vn' => 'Ly hôn',
            'jp' => '離婚',
        ],
        '99' => [
            'vn' => 'Chưa có thông tin',
            'jp' => '確認中'
        ]
    ],
    'religion' => [
        '1' => 'Phật giáo',
        '2' => 'Thiên Chúa giáo',
        '3' => 'Tin Lành',
        '4' => 'Hòa Hảo',
        '5' => 'Đạo Cơ Đốc',
        '6' => 'Không',
        '7' => 'Khác'
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
        ],
        '03' => [
            'vn' => 'Khác',
            'jp' => 'Other'
        ]
    ],
    'cardType' => [
        '1' => 'CMND',
        '2' => 'PASSPORT',
        '3' => 'VISA',
        '4' => 'BẰNG TỐT NGHIỆP',
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
        '4' => 'Đã có kết quả',
        '5' => 'Hoàn tất'
    ],
    'interviewResult' => [
        '0' => '-',
        '1' => 'Đậu',
        '2' => 'Rớt',
    ],
    'jlptResult' => [
        'Y' => 'Đậu',
        'N' => 'Rớt',
    ],
    'workTime' => [
        '1' => '1 năm',
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
    'input_test' => [
        '1' => 'Tính toán cơ bản',
        '2' => 'Tính toán nâng cao',
        '3' => 'Tiếng Nhật',
    ],
    'iqtest' => [
        'Câu 1',
        'Câu 2',
        'Câu 3',
        'Câu 4',
        'Câu 5',
        'Câu 6',
        'Câu 7',
        'Câu 8',
        'Câu 9',
        'Câu 10',
        'Câu 11',
        'Câu 12',
        'Câu 13',
        'Câu 14',
        'Câu 15',
        'Câu 16',
        'Câu 17',
        'Câu 18',
        'Câu 19',
        'Câu 20',
        'Câu 21',
        'Câu 22',
        'Câu 23',
        'Câu 24',
    ],
    'skills' => [
        '1' => 'Từ vựng',
        '2' => 'Ngữ pháp',
        '3' => 'Nghe hiểu',
        '4' => 'Đàm thoại',
        '5' => 'Số tự',
        '6' => 'Ngày giờ',
        '7' => 'Chào hỏi',
        '8' => 'Chữ cái',
    ],
    'jlpt_skills' => [
        '1' => 'Kiến thức chung',
        '2' => 'Đọc hiểu',
        '3' => 'Nghe',
    ],
    'jlpt_levels' => [
        'N5' => 'N5',
        'N4' => 'N4',
        'N3' => 'N3',
        'N2' => 'N2',
        'N1' => 'N1',
    ],
    'score' => [
        '1' => 'vocabulary_score',
        '2' => 'grammar_score',
        '3' => 'listening_score',
        '4' => 'conversation_score',
        '5' => 'num_word_score',
        '6' => 'datetime_score',
        '7' => 'greeting_score',
        '8' => 'alphabet_score',
    ],
    'jlpt_score' => [
        '1' => 'general_score',
        '2' => 'reading_score',
        '3' => 'listening_score',
    ],
    'testStatus' => [
        '1' => 'Chưa thi',
        '2' => 'Ngày thi',
        '3' => 'Đã thi',
        '4' => 'Đã có kết quả',
        '5' => 'Hoàn tất'
    ],

    //document
    'document' => [
        '1' => [
            'type' => 'Bằng cấp 2/cấp 3/Trung cấp/Cao đẳng/Đại học (Nộp cả bằng gốc)',
            'quantity' => '01 bản'
        ],
        '2' => [
            'type' => 'Chứng minh nhân dân/Thẻ căn cước (Photo công chứng)',
            'quantity' => '03 bản'
        ],
        '3' => [
            'type' => 'Hộ chiếu (Passport) _ Nộp cả hộ chiếu gốc (Photo công chứng)',
            'quantity' => '02 bản'
        ],
        '4' => [
            'type' => 'Số hộ khẩu (Photo công chứng)',
            'quantity' => '02 bản'
        ],
        '5' => [
            'type' => 'Giấy khai sinh (Bản sao)',
            'quantity' => '02 bản'
        ],
        '6' => [
            'type' => 'Sơ yếu lý lịch (Mẫu Công ty)',
            'quantity' => '01 bản'
        ],
        '7' => [
            'type' => 'Đơn xác nhận phụng dưỡng (Mẫu Công ty)',
            'quantity' => '03 bản'
        ],
        '8' => [
            'type' => 'Giấy đăng ký kết hôn (nếu có)',
            'quantity' => '01 bản'
        ],
        '9' => [
            'type' => 'Chứng minh nhân dân photo công chứng của những người trong đơn phụng dưỡng',
            'quantity' => '01 bản/người'
        ],
        '10' => [
            'type' => 'Hình 4 x 6 (Áo trắng, nền trắng)',
            'quantity' => '08 tấm'
        ],
        '11' => [
            'type' => 'Hình 3 x 4 (Áo trắng, nền trắng)',
            'quantity' => '08 tấm'
        ],
        '12' => [
            'type' => 'Hình 4,5 x 4,5 (Áo trắng, nền trắng)',
            'quantity' => '02 tấm'
        ],
    ],
    // user
    'scope' => [
        'Candidates' => 'Quản lý tuyển dụng Online',
        'Users' => 'Quản lý nhân viên',
        'Students' => 'Quản lý lao động',
        'Events' => 'Quản lý lịch công tác',
        'Orders' => 'Quản lý đơn hàng',
        'Jclasses' => 'Quản lý lớp học',
        'Jtests' => 'Quản lý kì thi',
        'JlptTests' => 'Quản lý JLPT',
        'Guilds' => 'Quản lý nghiệp đoàn',
        'Companies' => 'Quản lý công ty đối tác',
        'Presenters' => 'Quản lý cộng tác viên',
        'Jobs' => 'Quản lý nghề nghiệp',
        'Characteristics' => 'Quản lý tính cách',
        'Strengths' => 'Quản lý chuyên môn',
        'Purposes' => 'Quản lý mục đích xuất khẩu lao động',
        'AfterPlans' => 'Quản lý mục đích sau khi về nước'
    ],
    'companyType' => [
        '1' => 'Công ty phái cử',
        '2' => 'Công ty tiếp nhận'
    ],
    'permission' => [
        '1' => 'Chỉ đọc',
        '0' => 'Toàn quyền'
    ],
    'passwordDefault' => '123456789',

    // address vn
    'addressType' => [
        '1' => 'household', // ho khau thuong tru
        '2' => 'currentAddress'
    ],

    'addressLevel' => [
        'Thành phố' => [
            'en' => 'City',
            'jp' => '市'
        ],
        'Tỉnh' => [
            'en' => 'Province',
            'jp' => '省'
        ],
        'Quận' => [
            'en' => 'District',
            'jp' => '区'
        ],
        'Huyện' => [
            'en' => 'District',
            'jp' => '県'
        ],
        'Phường' => [
            'en' => 'Ward',
            'jp' => '郡'
        ],
        'Xã' => [
            'en' => 'Commune',
            'jp' => '社'
        ],
        'Thị trấn' => [
            'en' => 'Town',
            'jp' => '町'
        ],
        'Thị xã' => [
            'en' => 'Town',
            'jp' => '町'
        ],
    ],

    'addressJPLevel' => [
        'Thành phố' 
    ],

    // address jp
    'cityJP' => [
        '01' => [
            'rmj' => 'Hokkaido',
            'kj' => '北海道'
        ],
        '02' => [
            'rmj' => 'Aomori',
            'kj' => '青森県'
        ],
        '03' => [
            'rmj' => 'Iwate',
            'kj' => '岩手県'
        ],
        '04' => [
            'rmj' => 'Miyagi',
            'kj' => '宮城県'
        ],
        '05' => [
            'rmj' => 'Akita',
            'kj' => '秋田県'
        ],
        '06' => [
            'rmj' => 'Yamagata',
            'kj' => '山形県'
        ],
        '07' => [
            'rmj' => 'Fukushima',
            'kj' => '福島県'
        ],
        '08' => [
            'rmj' => 'Ibaraki',
            'kj' => '茨城県'
        ],
        '09' => [
            'rmj' => 'Tochigi',
            'kj' => '栃木県'
        ],
        '10' => [
            'rmj' => 'Gunma',
            'kj' => '群馬県'
        ],
        '11' => [
            'rmj' => 'Saitama',
            'kj' => '埼玉県'
        ],
        '12' => [
            'rmj' => 'Chiba',
            'kj' => '千葉県'
        ],
        '13' => [
            'rmj' => 'Tokyo',
            'kj' => '東京都'
        ],
        '14' => [
            'rmj' => 'Kanagawa',
            'kj' => '神奈川県'
        ],
        '15' => [
            'rmj' => 'Niigata',
            'kj' => '新潟県'
        ],
        '16' => [
            'rmj' => 'Toyama',
            'kj' => '富山県'
        ],
        '17' => [
            'rmj' => 'Ishikawa',
            'kj' => '石川県'
        ],
        '18' => [
            'rmj' => 'Fukui',
            'kj' => '福井県'
        ],
        '19' => [
            'rmj' => 'Yamanashi',
            'kj' => '山梨県'
        ],
        '20' => [
            'rmj' => 'Nagano',
            'kj' => '長野県'
        ],
        '21' => [
            'rmj' => 'Gifu',
            'kj' => '岐阜県'
        ],
        '22' => [
            'rmj' => 'Shizuoka',
            'kj' => '静岡県'
        ],
        '23' => [
            'rmj' => 'Aichi',
            'kj' => '愛知県'
        ],
        '24' => [
            'rmj' => 'Mie',
            'kj' => '三重県'
        ],
        '25' => [
            'rmj' => 'Shiga',
            'kj' => '滋賀県'
        ],
        '26' => [
            'rmj' => 'Kyoto',
            'kj' => '京都府'
        ],
        '27' => [
            'rmj' => 'Osaka',
            'kj' => '大阪府'
        ],
        '28' => [
            'rmj' => 'Hyogo',
            'kj' => '兵庫県'
        ],
        '29' => [
            'rmj' => 'Nara',
            'kj' => '奈良県'
        ],
        '30' => [
            'rmj' => 'Wakayama',
            'kj' => '和歌山県'
        ],
        '31' => [
            'rmj' => 'Tottori',
            'kj' => '鳥取県'
        ],
        '32' => [
            'rmj' => 'Shimane',
            'kj' => '島根県'
        ],
        '33' => [
            'rmj' => 'Okayama',
            'kj' => '岡山県'
        ],
        '34' => [
            'rmj' => 'Hiroshima',
            'kj' => '広島県'
        ],
        '35' => [
            'rmj' => 'Yamaguchi',
            'kj' => '山口県'
        ],
        '36' => [
            'rmj' => 'Tokushima',
            'kj' => '徳島県'
        ],
        '37' => [
            'rmj' => 'Kagawa',
            'kj' => '香川県'
        ],
        '38' => [
            'rmj' => 'Ehime',
            'kj' => '愛媛県'
        ],
        '39' => [
            'rmj' => 'Kochi',
            'kj' => '高知県'
        ],
        '40' => [
            'rmj' => 'Fukuoka',
            'kj' => '福岡県'
        ],
        '41' => [
            'rmj' => 'Saga',
            'kj' => '佐賀県'
        ],
        '42' => [
            'rmj' => 'Nagasaki',
            'kj' => '長崎県'
        ],
        '43' => [
            'rmj' => 'Kumamoto',
            'kj' => '熊本県'
        ],
        '44' => [
            'rmj' => 'Oita',
            'kj' => '大分県'
        ],
        '45' => [
            'rmj' => 'Miyazaki',
            'kj' => '宮崎県'
        ],
        '46' => [
            'rmj' => 'Kagoshima',
            'kj' => '鹿児島県'
        ],
        '47' => [
            'rmj' => 'Okinawa',
            'kj' => '沖縄県'
        ]

    ],

    // student
    'studentStatus' => [
        // '1' => 'Lịch hẹn',  ====> change into candidates
        '2' => 'Chưa đậu phỏng vấn',
        '3' => 'Đậu phỏng vấn',
        '4' => 'Đã xuất cảnh',
        '5' => 'Về nước',
        '6' => 'Bỏ trốn',
        '7' => 'Rút hồ sơ',
        '8' => 'Thanh lý hợp đồng',
        '9' => 'Xuất cảnh lần 2',
    ],
    'studentSubject' => [
        '1' => 'Bộ đội xuất ngũ',
        '2' => 'Đối tượng chính sách',
        '3' => 'Dân tộc thiểu số',
        '4' => 'Bình thường',
        '5' => 'Khác'
    ],

    // presenter
    'presenterType' => [
        '1' => 'Cá nhân',
        '2' => 'Công ty',
        '3' => 'Internet'
    ],

    // event
    'eventScope' => [
        '1' => 'Chỉ mình tôi',
        '2' => 'Toàn hệ thống'
    ],

    // .docx template
    'studentCodeTemplate' => 'LD-:date-:counter',
    'blackListTemplate' => 'Blacklist user :username, for :error',
    'currentAddressTemplate' => ':ward, :district, :city',
    'schoolTemplate' => ":schoolNameEN:eduLevelJP卒業\nTốt nghiệp trường :eduLevelVN :schoolNameVN",
    'folderImgTemplate' =>  ROOT . DS . 'webroot' . DS . 'img' . DS . 'templates',
    'resume' => [
        'filename' => '履歴書-:firstName.docx',
        'template' => 'resume.docx'
    ],
    'contract' => [
        'filename_jp' => '1. :firstName 日本での契約書（日本語).docx',
        'filename_vn' => '2. :firstName 日本での契約書（ベトナム語).docx',
    ],
    'eduPlan' => [
        'filename' => '1.10.docx',
        'template' => 'edu_plan.docx'
    ],
    'commitment' => [
        'filename' => '1.13.docx',
        'template' => 'commitment.docx'
    ],
    'dispatchLetter' => [
        'filename' => '技能実習生候補者のリスト.docx',
        'template' => 'dispatch-letter.docx'
    ],
    'dispatchLetterXlsx' => [
        'filename' => 'Mẫu đề nghị cấp thư phái cử.xlsx'
    ],
    'scheduleReportXlsx' => [
        'filename' => '12.xlsx'
    ],
    'orderDeclaration' => [
        'filename' => '1.20.docx',
        'template' => 'declaration.docx'
    ],
    'orderCertificate' => [
        'filename' => '1.28.docx',
        'template' => 'certificate.docx'
    ],
    'orderSchedule' => [
        'filename' => '4.8.docx',
        'template' => 'schedule.docx',
        'tableData' => [
            '1' => [
                'content' => '平仮名',
                'place' => "VINAGIMEX\n人材教育\nセンター"
            ],
            '2' => [
                'content' => "片仮名",
                'place' => "//"
            ],
            '3' => [
                'content' => "みんなの日本語教科書１第1課\n文法、「～は～です」、「も」、「の」、「だれ」、「何歳」\n練習、聴解練習、会話",
                'place' => "//"
            ],
            '4' => [
                'content' => "練習、聴解練習、会話、宿題チェック\nみんなの日本語教科書１第2課\n「これ、それ、あれ」、\n「この、～その～、あの～」、
                「そうです」、「～ですか～ですか」、
                「の」、「そうですか」
                ",
                'place' => "//"
            ],
            '5' => [
                'content' => "練習、聴解練習、会話\n練習、聴解練習、会話、宿題チェック",
                'place' => "//"
            ],
            '6' => [
                'content' => "みんなの日本語教科書１第3課\n「ここ、そこ、あそこ」、\n「～は場所です」、\n「どこ、どちら」、「の」\n練習、聴解練習、会話",
                'place' => "//"
            ],
            '7' => [
                'content' => "練習、聴解練習、会話、宿題チェック\nみんなの日本語教科書１第4課\n「時、分」、「～時に動詞」、\n「～から～まで」、「～と～」、「ね」",
                'place' => "//"
            ],
            '8' => [
                'content' => "練習、聴解練習、会話\n練習、聴解練習、会話、宿題チェック",
                'place' => "//"
            ],
            '9' => [
                'content' => "みんなの日本語教科書１第5課\n「～へ行く、来る、帰る」、\n「乗り物で～へ行く」、「人と行く」\n練習、聴解練習、会話",
                'place' => "//"
            ],
            '10' => [
                'content' => "練習、聴解練習、会話、宿題チェック\nみんなの日本語教科書１第6課\n「何を動詞」、「場所で動詞」、\n「ませんか」、「ましょう」",
                'place' => "//"
            ],
            '11' => [
                'content' => "練習、聴解練習、会話\n練習、聴解練習、会話、宿題チェック",
                'place' => "//"
            ],
            '12' => [
                'content' => "みんなの日本語教科書１第7課\n「工具で動詞」、「～語で」、\n「～にあげる」、「～にもらう」、\n「もう～ました」\n練習、聴解練習、会話",
                'place' => "//"
            ],
            '13' => [
                'content' => "練習、聴解練習、会話、宿題チェック\nみんなの日本語教科書１第8課\n「な形容詞、い形容詞」、\n「な形容詞＋名詞」、\n「い形容詞＋名詞」、\n「とても、あまり」、\n「～どう」、「～どんな」、\n「～ですが、～です」",
                'place' => "//"
            ],
            '14' => [
                'content' => "練習、聴解練習、会話\n練習、聴解練習、会話、宿題チェック",
                'place' => "//"
            ],
            '15' => [
                'content' => "みんなの日本語教科書１第9課\n「～がある」、「～がわかる」、\n「よく、沢山、少し、あまり、全然」、\n「どうして」、「～から」\n練習、聴解練習、会話",
                'place' => "//"
            ],
            '16' => [
                'content' => "練習、聴解練習、会話、宿題チェック\nみんなの日本語教科書１第10課\n「～がある」、「～がいる」、\n「場所に～がある」、\n「場所に～がいる」、\n「～は場所にある」、\n「～は場所にいる」、",
                'place' => "//"
            ],
            '17' => [
                'content' => "練習、聴解練習、会話\n練習、聴解練習、会話、宿題チェック",
                'place' => "//"
            ],
            '18' => [
                'content' => "みんなの日本語教科書１第11課\n「枚、台、つ、時間」、\n「週間、ヶ月、年間」、\n「回、だけ」、「どのくらい～ですか」\n練習、聴解練習、会話",
                'place' => "//"
            ],
            '19' => [
                'content' => "練習、聴解練習、会話、宿題チェック\nみんなの日本語教科書１第12課\n「～は～より形容詞」、\n「～は～ほど～じゃない」、\n「～と～とどちらが～ですか」、\n「～で～がいちばん～」",
                'place' => "//"
            ],
            '20' => [
                'content' => "日本語（専門用語に関する知識）\n工場内の安全衛生に関する用語\n業種別専門用語",
                'place' => "//"
            ],
            '21' => [
                'content' => "日本の歴史、文化",
                'place' => "//"
            ],
            '22' => [
                'content' => "生活に関するマナー、職場でのルール",
                'place' => "//"
            ],
            '23' => [
                'content' => "修得技能の目標、内容",
                'place' => "//"
            ],
            '24' => [
                'content' => "職場規律、就業規則、心構え",
                'place' => "//"
            ],
            '25' => [
                'content' => "安全衛生管理指導、ラジオ体操",
                'place' => "//"
            ],
            
        ]
    ],
    'orderScheduleRecord' => [
        'filename' => '1.29.docx',
        'template' => 'schedule_record.docx',
    ],
    'orderFees' => [
        'filename' => '1.21.docx',
        'template' => 'fees.docx',
    ],
    'cvTemplate' => [
        'filename' => ':firstName-CV.docx',
        'template' => 'cv.docx',
        'familyAdditional' => [
            0 => "続柄",
            1 => "使用教書",
            2 => "学習期間",
            3 => "既習課",
        ]
    ],
    'coverTemplate' => [
        'filename' => 'Bìa :order.docx',
        'template' => 'interview_cover.docx'
    ],
    'listCandidatesXlsx' => [
        'filename' => 'Danh sách ứng viên phỏng vấn.xlsx',
        'header' => [
            'A' => [
                'title' => 'No.',
                'width' => 4,
            ], 
            'B' => [
                'title' => '氏名',
                'width' => 36,
            ],
            'C' =>  [
                'title' => '生年月日', 
                'width' => 20,
            ],
            'D' => [
                'title' => '年齢',  
                'width' => 10,
            ],
            'E' => [
                'title' => '婚姻', 
                'width' => 10,
            ],
            'F' => [
                'title' => '日本語', 
                'width' => 10,
            ],
            'G' =>  [
                'title' => '計算',  
                'width' => 10,
            ],
            'H' => [
                'title' => 'クレペリン', 
                'width' => 14,
            ],
            'I' => [
                'title' => '握力 (kg)',
                'width' => 10,
            ],
            'I7' => '右',
            'J' => [
                'width' => 10,
            ],
            'J7' => '左',
            'K' => [
                'title' => '背筋力 (kg)',
                'width' => 14,
            ],
            'L' => [
                'title' => '血液型',
                'width' => 10,
            ],
            'M' => [
                'title' => '合格',
                'width' => 10,
            ],
            'N' => [
                'title' => '補欠',
                'width' => 10,
            ],
            'O' => [
                'title' => '備考',
                'width' => 10,
            ],
        ]
    ],
    'reportXlsx' => [
        'filename' => 'report.xlsx',
        'studentTitle' => 'DANH SÁCH LAO ĐỘNG',
        'classTitle' => 'DANH SÁCH LỚP HỌC',
        'testTitle' => "KẾT QUẢ KIỂM TRA NĂNG LỰC NHẬT NGỮ LỚP :class\nNGÀY THI :testDate\nBÀI THI: :testLessons",
        'branch' => 'CHI NHÁNH CÔNG TY VINAGIMEX., JSC (TP HCM)'
    ],
    'jlptResultXlsx' => [
        'filename' => 'Điểm_:level_:testDate.xlsx',
        'header' => "TRƯỜNG NHẬT NGỮ TÂM VIỆT\nKỲ THI NHẬT NGỮ TRÌNH ĐỘ :level\nNGÀY THI: :testDate\nBẢNG KẾT QUẢ",
        'branch' => 'CHI NHÁNH CÔNG TY VINAGIMEX., JSC (TP HCM)'
    ],
    'jlptReportXlsx' => [
        'filename' => 'BC_Điểm_JLPT.xlsx',
        'header' => "TRƯỜNG NHẬT NGỮ TÂM VIỆT\nBÁO CÁO ĐIỂM THI NĂNG LỰC NHẬT NGỮ",
        'branch' => 'CHI NHÁNH CÔNG TY VINAGIMEX., JSC (TP HCM)'
    ],
    'iqTestXlsx' => [
        'filename' => 'iq_test.xlsx',
    ],

    'vnDateFormatFull' => 'ngày :day tháng :month năm :year',
    'vnDateFormatShort' => ':day tháng :month năm :year',
    'jpKingYearName' => '作成',

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
    ],

    // message
    'successMessage' => [
        'add' => 'Đã lưu thông tin của :entity :name.',
        'addNoName' => 'Đã lưu thông tin thành công',
        'edit' => 'Thông tin của :entity :name đã được cập nhật.',
        'delete' => 'Đã xóa dữ liệu của :entity :name.',
        'recover' => 'Đã phục hồi dữ liệu của :entity :name.',
        'deleteNoName' => 'Đã xóa dữ liệu thành công',
        'updateProfile' => 'Hồ sơ cá nhân của bạn đã được cập nhật.',
        'updatePassword' => 'Mật khẩu của bạn đã được cập nhật.',
        'changeClass' => 'Học viên :name đã được chuyển qua lớp :class',
        'setScore' => 'Đã lưu điểm thành công',
        'setting' => 'Đã lưu cấu hình hiển thị',
        'resetPassword' => 'Đã khôi phục mật khẩu mặc định cho nhân viên :name thành công'
    ],
    'errorMessage' => [
        'add' => 'Đã có lỗi xảy ra. Xin hãy thử lại.',
        'edit' => 'Không thể cập nhật thông tin của :entity :name. Xin hãy thử lại.',
        'delete' => 'Không thể xóa dữ liệu của :entity :name. Xin hãy thử lại.',
        'recover' => 'Không thể phục hồi dữ liệu của :entity :name. Xin hãy thử lại.',
        'error' => 'Đã có lỗi xảy ra. Xin hãy thử lại.',
        'loginError' => 'Tên đăng nhập hoặc mật khẩu không đúng. Xin hãy thử lại.',
        'unAuthor' => 'Bạn không có quyền truy cập đến địa chỉ trên. Vui lòng Liên hệ Quản trị viên để được cấp quyền tương ứng.',
        'updatePassword' => 'Mật khẩu không đúng. Xin hãy nhập lại.',
        'changeClass' => 'Không thể chuyển học viên :name sang lớp :class',
        'setScore' => 'Không thể lưu điểm của phần thi :skill',
        'export' => '<p>Xuất file không thành công vì một số trường thiếu dữ liệu:</p><ul>:fields</ul>',
        'addJob' => 'Nghề :name đã tồn tại.'
    ],

    // table setting
    'cellWidth' => [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10',
        '11' => '11',
        '12' => '12',
    ],
    'orders' => [
        1 => [
            'field' => 'name',
            'title' => 'Đơn hàng',
            'defaultWidth' => 2
        ],
        2 => [
            'field' => 'interview_date',
            'title' => 'Ngày tuyển',
            'defaultWidth' => 2
        ],
        3 => [
            'field' => 'salary_from',
            'title' => 'Mức lương (từ)',
            'defaultWidth' => 2
        ],
        4 => [
            'field' => 'salary_to',
            'title' => 'Mức lương (đến)',
            'defaultWidth' => 2
        ],
        5 => [
            'field' => 'interview_type',
            'title' => 'Hình thức phỏng vấn',
            'defaultWidth' => 2
        ],
        6 => [
            'field' => 'skill_test',
            'title' => 'Thi tay nghề',
            'defaultWidth' => 2
        ],
        7 => [
            'field' => 'male_num',
            'title' => 'Số lượng nam',
            'defaultWidth' => 2
        ],
        8 => [
            'field' => 'female_num',
            'title' => 'Số lượng nữ',
            'defaultWidth' => 2
        ],
        9 => [
            'field' => 'height',
            'title' => 'Chiều cao (cm)',
            'defaultWidth' => 2
        ],
        10 => [
            'field' => 'weight',
            'title' => 'Cân nặng (kg)',
            'defaultWidth' => 2
        ],
        11 => [
            'field' => 'age_from',
            'title' => 'Độ tuổi (từ)',
            'defaultWidth' => 2
        ],
        12 => [
            'field' => 'age_to',
            'title' => 'Độ tuổi (đến)',
            'defaultWidth' => 2
        ],
        13 => [
            'field' => 'work_time',
            'title' => 'Thời gian làm việc',
            'defaultWidth' => 2
        ],
        14 => [
            'field' => 'work_at',
            'title' => 'Địa điểm làm việc',
            'defaultWidth' => 2
        ],
        15 => [
            'field' => 'departure_date',
            'title' => 'Ngày xuất cảnh',
            'defaultWidth' => 2
        ],
        16 => [
            'field' => 'status',
            'title' => 'Trạng thái',
            'defaultWidth' => 2
        ],
        17 => [
            'field' => 'company_id',
            'title' => 'Công ty tiếp nhận',
            'defaultWidth' => 2
        ],
        18 => [
            'field' => 'job_id',
            'title' => 'Nghề nghiệp',
            'defaultWidth' => 2
        ],
        19 => [
            'field' => 'created',
            'title' => 'Thời gian khởi tạo',
            'defaultWidth' => 2
        ],
        20 => [
            'field' => 'created_by',
            'title' => 'Người tạo',
            'defaultWidth' => 2
        ],
        21 => [
            'field' => 'modified',
            'title' => 'Thời gian sửa cuối',
            'defaultWidth' => 2
        ],
        22 => [
            'field' => 'modified_by',
            'title' => 'Người sửa cuối',
            'defaultWidth' => 2
        ],
    ]
];