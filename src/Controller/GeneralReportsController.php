<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralReportsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('ExportFile');
        $this->loadComponent('Util');
        $this->loadModel('Students');
        $this->loadModel('Orders');
        $this->missingFields = '';
    }

    public function isAuthorized($user)
    {
        return true;
    }

    public function student()
    {
        $presenters = $this->Students->Presenters->find('list')->order(['Presenters.name' => 'ASC']);
        $jclasses = $this->Students->Jclasses->find('list')->order(['Jclasses.name' => 'ASC']);
        $cities = TableRegistry::get('Cities')->find('list');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (!empty($data)) {
                $this->exportStudent($data);
            }
        }
        $this->set(compact('cities', 'presenters', 'jclasses'));
    }

    public function exportStudent($data)
    {
        $allStudents = $this->Students->find('all', ['contain' => [
            'Presenters',
            'Addresses' => function($q) {
                return $q->where(['Addresses.type' => '1']);
            },
            'Addresses.Cities',
            'Jclasses',
            'InterviewDeposits',
            'PhysicalExams' => function ($q) {
                return $q->order(['exam_date' => 'DESC']);
            },
        ]])->where(['Students.del_flag' => FALSE]);
        $condition = $data['std'];
        if (isset($condition['jclass_chk']) && $condition['jclass_chk'] == 'on' && !empty($condition['jclass'])) {
            $allStudents->matching('Jclasses', function($q) use ($condition) {
                return $q->where(['Jclasses.id' => $condition['jclass']]);
            });
        }
        if (isset($condition['status_chk']) && $condition['status_chk'] == 'on' && !empty($condition['status'])) {
            $allStudents->where(['Students.status' => $condition['status']]);
        }
        if (isset($condition['gender_chk']) && $condition['gender_chk'] == 'on' && !empty($condition['gender'])) {
            $allStudents->where(['Students.gender' => $condition['gender']]);
        }
        if (isset($condition['presenter_chk']) && $condition['presenter_chk'] == 'on' && !empty($condition['presenter'])) {
            $allStudents->where(['Students.presenter_id' => $condition['presenter']]);
        }
        if (isset($condition['edulevel_chk']) && $condition['edulevel_chk'] == 'on' && !empty($condition['edulevel'])) {
            $allStudents->where(['Students.educational_level' => $condition['edulevel']]);
        }
        if (isset($condition['birthday_chk']) && $condition['birthday_chk'] == 'on') {
            if (!empty($condition['birthday_from']) && empty($condition['birthday_to'])) {
                $from = $this->Util->convertDate($condition['birthday_from']);
                $allStudents->where(['Students.birthday >=' => $from]);
            } else if (empty($condition['birthday_from']) && !empty($condition['birthday_to'])) {
                $to = $this->Util->convertDate($condition['birthday_to']);
                $allStudents->where(['Students.birthday <=' => $to]);
            } else if (!empty($condition['birthday_from']) && !empty($condition['birthday_to'])) {
                $from = $this->Util->convertDate($condition['birthday_from']);
                $to = $this->Util->convertDate($condition['birthday_to']);
                $allStudents->where(function (QueryExpression $exp, Query $q) use($from, $to) {
                    return $exp->between(
                        'Students.birthday', 
                        $from->i18nFormat('yyyy-MM-dd'), 
                        $to->i18nFormat('yyyy-MM-dd'), 
                        'date'
                    );
                });
            }
        }
        if (isset($condition['phone_chk']) && $condition['phone_chk'] == 'on' && !empty($condition['phone'])) {
            $allStudents->where(function (QueryExpression $exp, Query $q) use ($condition) {
                return $exp->like('Students.phone', '%'.$condition['phone'].'%');
            });
        }
        if (isset($condition['hometown_chk']) && $condition['hometown_chk'] == 'on' && !empty($condition['hometown'])) {
            $allStudents->matching('Addresses', function($q) use ($condition) {
                return $q->where(['city_id' => $condition['hometown'], 'type' => 1]);
            });
        }
        if (isset($condition['enrolled_date_chk']) && $condition['enrolled_date_chk'] == 'on') {
            if (!empty($condition['enrolled_from']) && empty($condition['enrolled_to'])) {
                $from = $this->Util->convertDate($condition['enrolled_from']);
                $allStudents->where(['Students.enrolled_date >=' => $from]);
            } else if (empty($condition['enrolled_from']) && !empty($condition['enrolled_to'])) {
                $to = $this->Util->convertDate($condition['enrolled_to']);
                $allStudents->where(['Students.enrolled_date <=' => $to]);
            } else if (!empty($condition['enrolled_from']) && !empty($condition['enrolled_to'])) {
                $from = $this->Util->convertDate($condition['enrolled_from']);
                $to = $this->Util->convertDate($condition['enrolled_to']);
                $allStudents->where(function (QueryExpression $exp, Query $q) use($from, $to) {
                    return $exp->between(
                        'Students.enrolled_date', 
                        $from, 
                        $to, 
                        'date'
                    );
                });
            }
        }
        $allStudents->order(['Students.fullname' => 'ASC']);
        // Load config
        $reportConfig = Configure::read('reportXlsx');
        $studentStatus = Configure::read('studentStatus');
        $eduLevelConf = Configure::read('eduLevel');
        $financeStatus = Configure::read('financeStatus');
        $physResult = Configure::read('physResult');
        $jLessons = Configure::read('lessons');

        // init worksheet
        $spreadsheet = $this->ExportFile->setXlsxProperties();
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(11);
        
        $activeSheet->setShowGridLines(false);
        $activeSheet->setCellValue('A1', $reportConfig['branch']);
        $activeSheet->getStyle('A1:A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);
        $activeSheet
            ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
            ->mergeCells('B5:B6')->setCellValue('B5', 'Lao động');
        $activeSheet->getColumnDimension('A')->setWidth(6);
        $activeSheet->getColumnDimension('B')->setWidth(25);
        $col = 'B';
        if (isset($condition['status_chk']) && $condition['status_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Trạng thái'); 
            $activeSheet->getColumnDimension($col)->setWidth(20);
        }
        if (isset($condition['birthday_chk']) && $condition['birthday_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày sinh');
            $activeSheet->getColumnDimension($col)->setWidth(12);
        }
        if (isset($condition['gender_chk']) && $condition['gender_chk'] == 'on') {
            $left = $this->nextChar($col);
            $right = $this->nextChar($left);
            $activeSheet->mergeCells($left.'5:'.$right.'5')->setCellValue($left.'5', 'Giới tính');
            $activeSheet->setCellValue($left.'6', 'Nam')->setCellValue($right.'6', 'Nữ');
            $activeSheet->getColumnDimension($left)->setWidth(6);
            $activeSheet->getColumnDimension($right)->setWidth(6);
            $col = $right;
        }
        if (isset($condition['phone_chk']) && $condition['phone_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Số điện thoại');
            $activeSheet->getColumnDimension($col)->setWidth(15);
        }
        if (isset($condition['hometown_chk']) && $condition['hometown_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Quê quán');
            $activeSheet->getColumnDimension($col)->setWidth(20);
        }
        if (isset($condition['presenter_chk']) && $condition['presenter_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Người giới thiệu');
            $activeSheet->getColumnDimension($col)->setWidth(25);
        }
        if (isset($condition['edulevel_chk']) && $condition['edulevel_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Học vấn');
            $activeSheet->getColumnDimension($col)->setWidth(20);
        }
        if (isset($condition['enrolled_date_chk']) && $condition['enrolled_date_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày nhập học'); 
            $activeSheet->getColumnDimension($col)->setWidth(15);
        }
        if (isset($condition['jclass_chk']) && $condition['jclass_chk'] == 'on') {
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Lớp học'); 
            $activeSheet->getColumnDimension($col)->setWidth(15);
        }
        if (isset($data['additional']) && !empty($data['additional'])) {
            $additionalData = $data['additional'];
            if (isset($additionalData['lesson_chk']) && $additionalData['lesson_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Bài đang học'); 
                $activeSheet->getColumnDimension($col)->setWidth(20);
            }
            if (isset($additionalData['deposit_chk']) && $additionalData['deposit_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Tiền cọc'); 
                $activeSheet->getColumnDimension($col)->setWidth(25);
            }
            if (isset($additionalData['healthcheck_chk']) && $additionalData['healthcheck_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày khám sức khỏe'); 
                $activeSheet->getColumnDimension($col)->setWidth(25);
            }
            if (isset($additionalData['healthcheck_result_chk']) && $additionalData['healthcheck_result_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Kết quả khám sức khỏe'); 
                $activeSheet->getColumnDimension($col)->setWidth(30);
            }
        }
        
        $listStudents = [];
        $counter = 6;
        foreach ($allStudents as $key => $student) {
            $counter++;
            $studentData = [
                $key+1,
                $student->fullname,
            ];
            if (isset($condition['status_chk']) && $condition['status_chk'] == 'on') {
                $status = $student->status ? $studentStatus[$student->status] : '';
                array_push($studentData, $status);
            }
            if (isset($condition['birthday_chk']) && $condition['birthday_chk'] == 'on') {
                $birthday = $student->birthday ? $student->birthday->i18nFormat('dd-MM-yyyy') : '';
                array_push($studentData, $birthday);
            }
            if (isset($condition['gender_chk']) && $condition['gender_chk'] == 'on') {
                if ($student->gender == 'M') {
                    $male = 'x';
                    $female = '';
                } else {
                    $male = '';
                    $female = 'x';
                }
                array_push($studentData, $male, $female);
            }
            if (isset($condition['phone_chk']) && $condition['phone_chk'] == 'on') {
                $phone = $this->phoneFormat($student->phone);
                array_push($studentData, $phone);
            }
            if (isset($condition['hometown_chk']) && $condition['hometown_chk'] == 'on') {
                $hometown = !empty($student->addresses) ? $student->addresses[0]->city->name : '';
                array_push($studentData, $hometown);
            }
            if (isset($condition['presenter_chk']) && $condition['presenter_chk'] == 'on') {
                $presenter = !empty($student->presenter) ? $student->presenter->name : '';
                array_push($studentData, $presenter);
            }
            if (isset($condition['edulevel_chk']) && $condition['edulevel_chk'] == 'on') {
                $eduLevel = !empty($student->educational_level) ? $eduLevelConf[$student->educational_level]['vn'] : '';
                array_push($studentData, $eduLevel);
            }
            if (isset($condition['enrolled_date_chk']) && $condition['enrolled_date_chk'] == 'on') {
                $enrollDate = !empty($student->enrolled_date) ? $student->enrolled_date->i18nFormat('dd-MM-yyyy') : '';
                array_push($studentData, $enrollDate);
            }
            if (isset($condition['jclass_chk']) && $condition['jclass_chk'] == 'on') {
                $jclassName = '';
                if ($student->status == 4 && !empty($student->last_class)) {
                    # lao dong da dau pv
                    $jclassName = $student->last_class;
                } else {
                    $jclassName = !empty($student->_matchingData) ? $student->_matchingData['Jclasses']->name : '';
                }
                array_push($studentData, $jclassName);
            }
            if (isset($data['additional']) && !empty($data['additional'])) {
                $additionalData = $data['additional'];
                if (isset($additionalData['lesson_chk']) && $additionalData['lesson_chk'] == 'on') {
                    $jlesson = '';
                    if ($student->status == 4 && $student->last_lesson != NULL) {
                        # lao dong da dau pv
                        $jlesson = $jLessons[$student->last_lesson];
                    } else {
                        $jlesson = !empty($student->jclasses) ? $jLessons[$student->jclasses[0]->current_lesson] : '';
                    }
                    array_push($studentData, $jlesson);
                }
                if (isset($additionalData['deposit_chk']) && $additionalData['deposit_chk'] == 'on') {
                    $deposit = '';
                    if (!empty($student->interview_deposit) && !empty($student->interview_deposit->status)) {
                        $deposit = $financeStatus[$student->interview_deposit->status];
                    }
                    array_push($studentData, $deposit);
                }
                if (isset($additionalData['healthcheck_chk']) && $additionalData['healthcheck_chk'] == 'on') {
                    $healthcheck = !empty($student->physical_exams) ? $student->physical_exams[0]->exam_date->i18nFormat('dd-MM-yyyy') : '';
                    array_push($studentData, $healthcheck);
                }
                if (isset($additionalData['healthcheck_result_chk']) && $additionalData['healthcheck_result_chk'] == 'on') {
                    $result = '';
                    if (!empty($student->physical_exams) && !empty($student->physical_exams[0]->result)) {
                        $result = $physResult[$student->physical_exams[0]->result];
                    }
                    array_push($studentData, $result);
                }
            }
            array_push($listStudents, $studentData);
        }
        
        $activeSheet->fromArray($listStudents, NULL, 'A7');
        $activeSheet->getStyle('A5:'. $col . $counter)->getAlignment()->setWrapText(true);
        $activeSheet->getStyle('A5:'. $col . $counter)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Style\Border::BORDER_THIN,
                ]
            ],
            'alignment' => [
                'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $activeSheet->getStyle('A5:'.$col.'6')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $activeSheet->getStyle('A7:A'.$counter)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $spreadsheet = $this->ExportFile->generateFooter($spreadsheet, $counter+1, $col);
        $spreadsheet->getActiveSheet()->freezePane('A7');
        // export XLSX file for download
        $this->ExportFile->export($spreadsheet, $reportConfig['filename']);
        exit;
    }

    public function order()
    {
        $disCompanies = TableRegistry::get('Companies')->find('list')
                                                       ->where(['type' => '1', 'del_flag' => FALSE])
                                                       ->order(['Companies.name_romaji' => 'ASC']);
        $companies = TableRegistry::get('Companies')->find('list')
                                                    ->where(['type' => '2', 'del_flag' => FALSE])
                                                    ->order(['Companies.name_romaji' => 'ASC']);
        $guilds = TableRegistry::get('Guilds')->find('list')->where(['del_flag' => FALSE])
                                              ->order(['Guilds.name_romaji' => 'ASC']);
        $jclasses = $this->Students->Jclasses->find('list')->order(['Jclasses.name' => 'ASC']);
        $orders = $this->Students->Orders->find()
                    ->where(['del_flag' => FALSE])
                    ->map(function ($row) {
                        $row->name = $row->name . ' (' . $row->interview_date->i18nFormat('dd/MM/yyyy') . ')';
                            return $row;
                        })
                    ->combine('id', 'name')
                    ->toArray();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $this->exporOrder($data);
        }
        $this->set(compact('companies', 'disCompanies', 'guilds', 'orders', 'jclasses'));
    }

    public function exporOrder($data)
    {
        try {
            $allOrders = $this->Orders->find('all', ['contain' => [
                'Students' => function ($q) {
                    return $q->where(['Students.del_flag' => FALSE]);
                },
                'Students.InterviewDeposits',
                'Students.PhysicalExams' => function ($q) {
                    return $q->order(['exam_date' => 'DESC']);
                },
                'Students.Jclasses',
                'Guilds' => function ($q) {
                    return $q->where(['Guilds.del_flag' => FALSE]);
                },
                'Companies' => function ($q) {
                    return $q->where(['Companies.del_flag' => FALSE]);
                },
                'DisCompanies' => function ($q) {
                    return $q->where(['DisCompanies.del_flag' => FALSE]);
                },
                'Jobs' => function ($q) {
                    return $q->where(['Jobs.del_flag' => FALSE]);
                }
            ]])->where(['Orders.del_flag' => FALSE]);
            $condition = $data['order'];
            if (isset($condition['id']) && !empty($condition['id'])) {
                $allOrders->where(['Orders.id' => $condition['id']]);
            }
            if (isset($condition['interview_date_chk']) && $condition['interview_date_chk'] == 'on') {
                if (!empty($condition['interview_date_from']) && empty($condition['interview_date_to'])) {
                    $from = $this->Util->convertDate($condition['interview_date_from']);
                    $allOrders->where(['Orders.interview_date >=' => $from]);
                } else if (empty($condition['interview_date_from']) && !empty($condition['interview_date_to'])) {
                    $to = $this->Util->convertDate($condition['interview_date_to']);
                    $allOrders->where(['Orders.interview_date <=' => $to]);
                } else if (!empty($condition['interview_date_from']) && !empty($condition['interview_date_to'])) {
                    $from = $this->Util->convertDate($condition['interview_date_from']);
                    $to = $this->Util->convertDate($condition['interview_date_to']);
                    $allOrders->where(['Orders.interview_date >=' => $from, 'Orders.interview_date <=' => $to]);
                }
            }
            if (isset($condition['departure_month_chk']) && $condition['departure_month_chk'] == 'on') {
                if (!empty($condition['departure_month_from']) && empty($condition['departure_month_to'])) {
                    $from = new Time('01-' . $condition['departure_month_from']);
                    $allOrders->where(['Orders.departure_date >=' => $from->i18nFormat('yyyy-MM')]);
                } else if (empty($condition['departure_month_from']) && !empty($condition['departure_month_to'])) {
                    $to = $this->Util->getLastDayOfMonth('01-' . $condition['departure_month_to']);
                    $to = new Time($to);
                    $allOrders->where(['Orders.departure_date <=' => $to->i18nFormat()]);
                } else if (!empty($condition['departure_month_from']) && !empty($condition['departure_month_to'])) {
                    $from = new Time('01-' . $condition['departure_month_from']);
                    $to = $this->Util->getLastDayOfMonth('01-' . $condition['departure_month_to']);
                    $to = new Time($to);
                    $allOrders->where(['Orders.departure_date >=' => $from->i18nFormat('yyyy-MM'), 'Orders.departure_date <=' => $to->i18nFormat('yyyy-MM')]);
                }
            }
            if (isset($condition['departure_date_chk']) && $condition['departure_date_chk'] == 'on') {
                if (!empty($condition['departure_date_from']) && empty($condition['departure_date_to'])) {
                    $from = $this->Util->convertDate($condition['departure_date_from']);
                    $allOrders->where(['Orders.departure >=' => $from]);
                } else if (empty($condition['departure_date_from']) && !empty($condition['departure_date_to'])) {
                    $to = $this->Util->convertDate($condition['departure_date_to']);
                    $allOrders->where(['Orders.departure <=' => $to]);
                } else if (!empty($condition['departure_date_from']) && !empty($condition['departure_date_to'])) {
                    $from = $this->Util->convertDate($condition['departure_date_from']);
                    $to = $this->Util->convertDate($condition['departure_date_to']);
                    $allOrders->where(['Orders.departure >=' => $from, 'Orders.departure <=' => $to]);
                }
            }
            if (isset($condition['discompany_chk']) && $condition['discompany_chk'] == 'on' && !empty($condition['discompany'])) {
                $allOrders->where(['Orders.dis_company_id' => $condition['discompany']]);
            }
            if (isset($condition['company_chk']) && $condition['company_chk'] == 'on' && !empty($condition['company'])) {
                $allOrders->where(['Orders.company_id' => $condition['company']]);
            }
            if (isset($condition['guild_chk']) && $condition['guild_chk'] == 'on' && !empty($condition['guild'])) {
                $allOrders->where(['Orders.guild_id' => $condition['guild']]);
            }
            $allOrders->order(['Orders.interview_date' => 'DESC']);
            // Load config
            $reportConfig = Configure::read('reportXlsx');
            $interviewResult = Configure::read('interviewResult');
            $financeStatus = Configure::read('financeStatus');
            $physResult = Configure::read('physResult');
            $jLessons = Configure::read('lessons');

            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);
            
            $activeSheet->setShowGridLines(false);
            $activeSheet->setCellValue('A1', $reportConfig['branch']);
            $activeSheet->getStyle('A1:A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);
            $activeSheet
                ->mergeCells('A5:A6')->setCellValue('A5', 'STT')
                ->mergeCells('B5:B6')->setCellValue('B5', 'Đơn hàng');
            $activeSheet->getColumnDimension('A')->setWidth(6);
            $activeSheet->getColumnDimension('B')->setWidth(25);
            $col = 'B';
            if (isset($condition['interview_date_chk']) && $condition['interview_date_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày phỏng vấn'); 
                $activeSheet->getColumnDimension($col)->setWidth(15);
            }
            if (isset($condition['departure_month_chk']) && $condition['departure_month_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày xuất cảnh dự kiến'); 
                $activeSheet->getColumnDimension($col)->setWidth(25);
            }
            if (isset($condition['departure_date_chk']) && $condition['departure_date_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày xuất cảnh chính thức'); 
                $activeSheet->getColumnDimension($col)->setWidth(25);
            }
            if (isset($condition['discompany_chk']) && $condition['discompany_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Công ty phái cử'); 
                $activeSheet->getColumnDimension($col)->setWidth(35);
            }
            if (isset($condition['company_chk']) && $condition['company_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Công ty tiếp nhận'); 
                $activeSheet->getColumnDimension($col)->setWidth(35);
            }
            if (isset($condition['guild_chk']) && $condition['guild_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Nghiệp đoàn quản lý'); 
                $activeSheet->getColumnDimension($col)->setWidth(35);
            }
            # for student info
            $col = $this->nextChar($col);
            $studentCol = $col;
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Lao động'); 
            $activeSheet->getColumnDimension($col)->setWidth(25);

            # for interview result
            $col = $this->nextChar($col);
            $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Kết quả phỏng vấn'); 
            $activeSheet->getColumnDimension($col)->setWidth(20);

            if (isset($condition['jclass_chk']) && $condition['jclass_chk'] == 'on') {
                $col = $this->nextChar($col);
                $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Lớp học'); 
                $activeSheet->getColumnDimension($col)->setWidth(15);
            }
            if (isset($data['additional']) && !empty($data['additional'])) {
                $additionalData = $data['additional'];
                if (isset($additionalData['lesson_chk']) && $additionalData['lesson_chk'] == 'on') {
                    $col = $this->nextChar($col);
                    $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Bài đang học'); 
                    $activeSheet->getColumnDimension($col)->setWidth(20);
                }
                if (isset($additionalData['deposit_chk']) && $additionalData['deposit_chk'] == 'on') {
                    $col = $this->nextChar($col);
                    $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Tiền cọc'); 
                    $activeSheet->getColumnDimension($col)->setWidth(25);
                }
                if (isset($additionalData['healthcheck_chk']) && $additionalData['healthcheck_chk'] == 'on') {
                    $col = $this->nextChar($col);
                    $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Ngày khám sức khỏe'); 
                    $activeSheet->getColumnDimension($col)->setWidth(25);
                }
                if (isset($additionalData['healthcheck_result_chk']) && $additionalData['healthcheck_result_chk'] == 'on') {
                    $col = $this->nextChar($col);
                    $activeSheet->mergeCells($col.'5:'.$col.'6')->setCellValue($col.'5', 'Kết quả khám sức khỏe'); 
                    $activeSheet->getColumnDimension($col)->setWidth(30);
                }
            }

            $listStudents = [];
            $counter = 6;

            $selectedJclass = NULL;
            if (isset($condition['jclass_chk']) && $condition['jclass_chk'] == 'on' && !empty($condition['jclass'])) {
                $selectedJclass = $this->Students->Jclasses->get($condition['jclass'])->name;
            }
            foreach ($allOrders as $key => $order) {
                $start = $counter + 1;
                if (!empty($order->students)) {
                    $studentData = [];
                    $have_student = FALSE;
                    foreach ($order->students as $index => $student) {
                        $jclassName = '';
                        if ($student->status == 4 && !empty($student->last_class) ) {
                            # lao dong da dau pv
                            $jclassName = $student->last_class;
                        } else {
                            $jclassName = !empty($student->jclasses) ? $student->jclasses[0]->name : '';
                        }
                        if ((!empty($selectedJclass) && $jclassName === $selectedJclass) || empty($selectedJclass)) {
                            if (isset($condition['interview_result_chk']) && $condition['interview_result_chk'] == 'on') {
                                if ($condition['interview_result'] != NULL) {
                                    if ($student->_joinData->result !== $condition['interview_result']) {
                                        if ($index == sizeof($order->students) - 1 && !$have_student) {
                                            array_push($listStudents, []);
                                        }
                                        continue;
                                    }
                                }
                            }
                            $have_student = TRUE;
                            $counter++;
                            $studentData = [$student->fullname, $interviewResult[$student->_joinData->result]];
                            if (isset($condition['jclass_chk']) && $condition['jclass_chk'] == 'on') {
                                array_push($studentData, $jclassName);
                            }
                            if (isset($data['additional']) && !empty($data['additional'])) {
                                $additionalData = $data['additional'];
                                if (isset($additionalData['lesson_chk']) && $additionalData['lesson_chk'] == 'on') {
                                    $jlesson = '';
                                    if ($student->status == 4 && $student->last_lesson != NULL) {
                                        # lao dong da dau pv
                                        $jlesson = $jLessons[$student->last_lesson];
                                    } else {
                                        $jlesson = !empty($student->jclasses) ? $jLessons[$student->jclasses[0]->current_lesson] : '';
                                    }
                                    array_push($studentData, $jlesson);
                                }
                                if (isset($additionalData['deposit_chk']) && $additionalData['deposit_chk'] == 'on') {
                                    $deposit = '';
                                    if (!empty($student->interview_deposit) && !empty($student->interview_deposit->status)) {
                                        $deposit = $financeStatus[$student->interview_deposit->status];
                                    }
                                    array_push($studentData, $deposit);
                                }
                                if (isset($additionalData['healthcheck_chk']) && $additionalData['healthcheck_chk'] == 'on') {
                                    $healthcheck = !empty($student->physical_exams) ? $student->physical_exams[0]->exam_date->i18nFormat('dd-MM-yyyy') : '';
                                    array_push($studentData, $healthcheck);
                                }
                                if (isset($additionalData['healthcheck_result_chk']) && $additionalData['healthcheck_result_chk'] == 'on') {
                                    $result = '';
                                    if (!empty($student->physical_exams) && !empty($student->physical_exams[0]->result)) {
                                        $result = $physResult[$student->physical_exams[0]->result];
                                    }
                                    array_push($studentData, $result);
                                }
                            }
                            array_push($listStudents, $studentData);
                        }
                        if ($index == sizeof($order->students) - 1 && !$have_student) {
                            array_push($listStudents, []);
                        }
                    }
                } else {
                    array_push($listStudents, []);
                }
                if ($counter < $start) {
                    $counter = $start;
                }
                $end = $counter;
                
                $activeSheet->mergeCells('A'.$start.':A'.$end)->setCellValue('A'.$start, $key+1);
                $activeSheet->mergeCells('B'.$start.':B'.$end)->setCellValue('B'.$start, $order->name);
                $ccol = 'B';
                if (isset($condition['interview_date_chk']) && $condition['interview_date_chk'] == 'on') {
                    $ccol = $this->nextChar($ccol);
                    $interviewDate = !empty($order->interview_date) ? $order->interview_date->i18nFormat('dd-MM-yyyy') : '';
                    $activeSheet->mergeCells($ccol.$start.':'.$ccol.$end)->setCellValue($ccol.$start, $interviewDate);
                }
                if (isset($condition['departure_month_chk']) && $condition['departure_month_chk'] == 'on') {
                    $ccol = $this->nextChar($ccol);
                    $departureMonth = '';
                    if (!empty($order->departure_date)) {
                        $tmp = new Time($order->departure_date . '-01');
                        $departureMonth = $tmp->i18nFormat('MM-yyyy');
                    }
                    $activeSheet->mergeCells($ccol.$start.':'.$ccol.$end)->setCellValue($ccol.$start, $departureMonth);
                }
                if (isset($condition['departure_date_chk']) && $condition['departure_date_chk'] == 'on') {
                    $ccol = $this->nextChar($ccol);
                    $departureDate = !empty($order->departure) ? $order->departure->i18nFormat('dd-MM-yyyy') : '';
                    $activeSheet->mergeCells($ccol.$start.':'.$ccol.$end)->setCellValue($ccol.$start, $departureDate);
                }
                if (isset($condition['discompany_chk']) && $condition['discompany_chk'] == 'on') {
                    $ccol = $this->nextChar($ccol);
                    $dis_company_name = $order->dis_company ? $order->dis_company->name_romaji : '';
                    $activeSheet->mergeCells($ccol.$start.':'.$ccol.$end)->setCellValue($ccol.$start, $dis_company_name);
                }
                if (isset($condition['company_chk']) && $condition['company_chk'] == 'on') {
                    $ccol = $this->nextChar($ccol);
                    $companyName = $order->company ? $order->company->name_romaji : '';
                    $activeSheet->mergeCells($ccol.$start.':'.$ccol.$end)->setCellValue($ccol.$start, $companyName);
                }
                if (isset($condition['guild_chk']) && $condition['guild_chk'] == 'on') {
                    $ccol = $this->nextChar($ccol);
                    $activeSheet->mergeCells($ccol.$start.':'.$ccol.$end)->setCellValue($ccol.$start, $order->guild->name_romaji);
                }
            }
            $activeSheet->fromArray($listStudents, NULL, $studentCol.'7');
            $activeSheet->getStyle('A5:'. $col . $counter)->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('A5:'. $col . $counter)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Style\Border::BORDER_THIN,
                    ]
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $activeSheet->getStyle('A5:'.$col.'6')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
            $activeSheet->getStyle('A7:A'.$counter)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $spreadsheet = $this->ExportFile->generateFooter($spreadsheet, $counter+1, $col);
            $spreadsheet->getActiveSheet()->freezePane('A7');
            // export XLSX file for download
            $this->ExportFile->export($spreadsheet, $reportConfig['filename']);
            exit;

            
        } catch (Exception $e) {
            Log::write('debug', $e);
        }
    }

    public function nextChar($char)
    {
        $nextChar = ord($char) + 1;
        return chr($nextChar);
    }
    public function phoneFormat($number)
    {
        $number = preg_replace("/[^0-9]/", "", $number);
        if (strlen($number) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1 $2 $3", $number);
        } elseif (strlen($number) == 11) {
            return preg_replace("/([0-9]{4})([0-9]{3})([0-9]{4})/", "$1 $2 $3", $number);
        } else {
            return $number;
        }
    }
}