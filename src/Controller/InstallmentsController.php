<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Log\Log;
use Cake\Utility\Text;
use Cake\I18n\Time;

use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
/**
 * Installments Controller
 *
 * @property \App\Model\Table\InstallmentsTable $Installments
 *
 * @method \App\Model\Entity\Installment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InstallmentsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->entity = 'đợt thu phí';
        $this->loadComponent('ExportFile');
    }

    public function isAuthorized($user)
    {
        if ($this->Auth->user('role')['name'] != 'admin') {
            return false;
        }
        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {   
        $query = $this->request->getQuery();
        if (!empty($query)) {
            $allInstallments = $this->Installments->find();
            if (!isset($query['records']) || empty($query['records'])) {
                $query['records'] = $this->defaultDisplay;
            }
            if (isset($query['name']) && !empty($query['name'])) {
                $allInstallments->where(function (QueryExpression $exp, Query $q) use ($query) {
                    return $exp->like('name', '%'.$query['name'].'%');
                });
            }
            if (isset($query['f_quarter']) && !empty($query['f_quarter'])) {
                $allInstallments->where(['Installments.quarter' => $query['f_quarter']]);
            }
            if (isset($query['f_quarter_year']) && !empty($query['f_quarter_year'])) {
                $allInstallments->where(['Installments.quarter_year' => $query['f_quarter_year']]);
            }
            if (isset($query['f_admin_company']) && !empty($query['f_admin_company'])) {
                $allInstallments->where(['Installments.admin_company_id' => $query['f_admin_company']]);
            }
            if (isset($query['f_created_by']) && !empty($query['f_created_by'])) {
                $allInstallments->where(['Installments.created_by' => $query['f_created_by']]);
            }
            if (isset($query['f_modified_by']) && !empty($query['f_modified_by'])) {
                $allInstallments->where(['Installments.modified_by' => $query['f_modified_by']]);
            }
            if (!isset($query['sort'])) {
                $allInstallments->order(['Installments.created' => 'DESC']);
            }
        }
        else {
            $query['records'] = $this->defaultDisplay;
            $allInstallments = $this->Installments->find()->order(['Installments.created' => 'DESC']);
        }
        $this->paginate = [
            'contain' => [
                'AdminCompanies',
                'CreatedByUsers',
                'ModifiedByUsers'
            ],
            'sortWhitelist' => ['name', 'quarter', 'quarter_year'],
            'limit' => $query['records'],
        ];
        # get installments report
        $report = [];
        $defaultAdminCompany = TableRegistry::get('AdminCompanies')->find()->where(['deleted' => FALSE])->first();
        if (!empty($defaultAdminCompany)) {
            $report = $this->generateReport($defaultAdminCompany->id);
        }

        $installments = $this->paginate($allInstallments);
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $usersTable = TableRegistry::get('Users');
        $allUsers = $usersTable->find('list')->where(['del_flag' => false]);
        $this->set(compact('report', 'installments', 'allUsers', 'adminCompanies', 'query'));
    }

    public function generateReport($adminCompanyId)
    {
        $now = Time::now();
        $currentYear = $now->year;
        $prevYear = $currentYear - 1;
        $results = $this->Installments->find()
            ->contain(['InstallmentFees'])
            ->where([
                'Installments.admin_company_id' => $adminCompanyId,
                'OR' => [['Installments.quarter_year' => $prevYear], ['Installments.quarter_year' => $currentYear]]
                ])
            ->toArray();
        $report = [
            $prevYear => [],
            $currentYear => []
        ];
        foreach ($results as $key => $installment) {
            $totalVN = $totalJP = $managementFee = $airTicketFee = $trainingFee = $otherFees = 0;
            foreach ($installment->installment_fees as $key => $value) {
                if (isset($value->total_vn)) {
                    $totalVN += $value->total_vn;
                }
                if (isset($value->total_jp)) {
                    $totalJP += $value->total_jp;
                }
                $managementFee += $value->management_fee;
                $airTicketFee += $value->air_ticket_fee;
                $trainingFee += $value->training_fee;
                $otherFees += $value->other_fees;
            }
            if (array_key_exists($installment->quarter, $report[$installment->quarter_year])) {
                $report[$installment->quarter_year][$installment->quarter] = [
                    'managementFee' => $managementFee + $report[$installment->quarter_year][$installment->quarter]['managementFee'],
                    'airTicketFee' => $airTicketFee + $report[$installment->quarter_year][$installment->quarter]['airTicketFee'],
                    'trainingFee' => $trainingFee + $report[$installment->quarter_year][$installment->quarter]['trainingFee'],
                    'otherFees' => $otherFees + $report[$installment->quarter_year][$installment->quarter]['otherFees'],
                    'totalVN' => $totalVN + $report[$installment->quarter_year][$installment->quarter]['totalVN'],
                    'totalJP' => $totalJP + $report[$installment->quarter_year][$installment->quarter]['totalJP']
                ];
            } else {
                $report[$installment->quarter_year][$installment->quarter] = [
                    'managementFee' => $managementFee,
                    'airTicketFee' => $airTicketFee,
                    'trainingFee' => $trainingFee,
                    'otherFees' => $otherFees,
                    'totalVN' => $totalVN,
                    'totalJP' => $totalJP
                ];
            }
        }
        return $report;
    }

    public function report($adminCompanyId = null)
    {
        $this->request->allowMethod('ajax');
        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $report = $this->generateReport($adminCompanyId);
            $resp = [
                'status' => 'success',
                'report' => $report 
            ];
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }

    /**
     * View method
     *
     * @param string|null $id Installment id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $installment = $this->Installments->get($id, [
            'contain' => [
                'AdminCompanies',
                'InstallmentFees' => ['sort' => ['name_romaji' => 'ASC', 'invoice_date' => 'ASC']], 
                'InstallmentFees.Guilds'
            ]
        ]);
        $this->set(compact('installment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $installment = $this->Installments->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $installment = $this->Installments->patchEntity($installment, $data, ['associated' => ['InstallmentFees']]);
            $installment = $this->Installments->setAuthor($installment, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Installments->save($installment)) {
                $this->Flash->success($this->successMessage['addNoName']);
                return $this->redirect(['action' => 'edit', $installment->id]);
            }
            $this->Flash->error($this->errorMessage['add']);
        }
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $guilds = TableRegistry::get('Guilds')->find('list')->where(['del_flag' => false]);
        $this->set(compact('installment', 'guilds', 'adminCompanies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Installment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $installment = $this->Installments->get($id, [
            'contain' => [
                'AdminCompanies',
                'InstallmentFees' => ['sort' => ['name_romaji' => 'ASC', 'invoice_date' => 'ASC']], 
                'InstallmentFees.Guilds'
                ]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $installment = $this->Installments->patchEntity($installment, $data);
            $installment = $this->Installments->setAuthor($installment, $this->Auth->user('id'), $this->request->getParam('action'));
            if ($this->Installments->save($installment)) {
                $this->Flash->success($this->successMessage['addNoName']);
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->Flash->error($this->errorMessage['error']);
        }
        $adminCompanies = TableRegistry::get('AdminCompanies')->find('list')->where(['deleted' => FALSE])->toArray();
        $guilds = TableRegistry::get('Guilds')->find('list')->where(['del_flag' => false]);
        $this->set(compact('installment', 'guilds', 'adminCompanies'));
        $this->render('/Installments/add');
    }

    public function export($id = null)
    {
        $config = Configure::read('installmentFeesXlsx');
        $installmentStatus = Configure::read('installmentStatus');
        try {
            # get data
            $installment = $this->Installments->get($id, [
                'contain' => [
                    'AdminCompanies',
                    'InstallmentFees' => ['sort' => ['name_romaji' => 'ASC', 'invoice_date' => 'ASC']], 
                    'InstallmentFees.Guilds'
                    ]
            ]);
            // init worksheet
            $spreadsheet = $this->ExportFile->setXlsxProperties();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(130);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);
            $spreadsheet->getActiveSheet()->setShowGridLines(false);
            $spreadsheet->getActiveSheet()
                ->mergeCells('A1:L1')->setCellValue('A1', 'BẢNG THEO DÕI CHI PHÍ')
                ->mergeCells('A2:L2')->setCellValue('A2', $installment->name)
                ->mergeCells('A3:L3')->setCellValue('A3', 'Phân nhánh: '. $installment->admin_company->short_name);
            
            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
            $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
            $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            $spreadsheet->getActiveSheet()->getStyle('A1:A3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $spreadsheet->getActiveSheet()->getStyle('A4:L4')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $spreadsheet->getActiveSheet()
                ->setCellValue('A4', 'STT')
                ->setCellValue('B4', 'Tên Nghiệp Đoàn')
                ->setCellValue('C4', 'Phí quản lý (¥)')
                ->setCellValue('D4', 'Vé máy bay (¥)')
                ->setCellValue('E4', 'Phí đào tạo (¥)')
                ->setCellValue('F4', 'Khoản khác (¥)')
                ->setCellValue('G4', 'Tổng cộng (¥)')
                ->setCellValue('H4', 'Tổng tiền vào tài khoản (₫)')
                ->setCellValue('I4', 'Ngày gửi hóa đơn')
                ->setCellValue('J4', 'Ngày nhận tiền')
                ->setCellValue('K4', 'Trạng thái')
                ->setCellValue('L4', 'Ghi chú');
            
            $installmentFees = [];
            $counter = 4;
            $no = 0;
            $total = count($installment->installment_fees);
            $cacheGuildId = null;
            $mergeRows = array();
            $start = $end = $counter;
            $total_vn = $total_jp = $sum_management_fee = $sum_air_ticket_fee = $sum_training_fee = $sum_other_fees = 0;
            foreach ($installment->installment_fees as $key => $value) {
                $currentGuildId = $value->guild->id;
                if ($currentGuildId == $cacheGuildId) {
                    $end = $counter + 1;
                    if ($key == $total - 1) {
                        array_push($mergeRows, [$start, $end]);
                    }
                } else {
                    $no += 1;
                    $cacheGuildId = $currentGuildId;
                    if ($start != $end) {
                        array_push($mergeRows, [$start, $end]);
                    }
                    $start = $end = $counter + 1;
                }
                if (isset($value->total_vn)) {
                    $total_vn += $value->total_vn;
                }
                if (isset($value->total_jp)) {
                    $total_jp += $value->total_jp;
                }
                $sum_management_fee += $value->management_fee;
                $sum_air_ticket_fee += $value->air_ticket_fee;
                $sum_training_fee += $value->training_fee;
                $sum_other_fees += $value->other_fees;
                $data = [
                    $no,
                    $value->guild->name_romaji,
                    number_format($value->management_fee),
                    number_format($value->air_ticket_fee),
                    number_format($value->training_fee),
                    number_format($value->other_fees),
                    number_format($value->total_jp),
                    isset($value->total_vn) ? number_format($value->total_vn) : '',
                    $value->invoice_date ? $value->invoice_date->i18nFormat('dd/MM/yyyy') : '',
                    $value->receiving_money_date ? $value->receiving_money_date->i18nFormat('dd/MM/yyyy') : '',
                    $installmentStatus[$value->status],
                    $value->notes
                ];
                array_push($installmentFees, $data);
                $counter++;
            }
            // fill data to table
            $spreadsheet->getActiveSheet()->fromArray($installmentFees, NULL, 'A5');
            foreach ($mergeRows as $key => $rows) {
                $spreadsheet->getActiveSheet()
                    ->mergeCells("B{$rows[0]}:B{$rows[1]}")
                    ->mergeCells("A{$rows[0]}:A{$rows[1]}");
            }
            $counter++;
            $spreadsheet->getActiveSheet()
                ->mergeCells("A{$counter}:B{$counter}")->setCellValue("A{$counter}", 'Tổng kết')
                ->setCellValue("C{$counter}", number_format($sum_management_fee))
                ->setCellValue("D{$counter}", number_format($sum_air_ticket_fee))
                ->setCellValue("E{$counter}", number_format($sum_training_fee))
                ->setCellValue("F{$counter}", number_format($sum_other_fees))
                ->setCellValue("G{$counter}", number_format($total_jp));
            if ($total_vn != 0) {
                $spreadsheet->getActiveSheet()->setCellValue("H{$counter}", number_format($total_vn));
            }
            $spreadsheet->getActiveSheet()->getStyle("A5:L{$counter}")->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle("A4:L{$counter}")->applyFromArray([
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
            $spreadsheet->getActiveSheet()->getStyle("B4:B{$counter}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $spreadsheet->getActiveSheet()->getStyle("L5:L{$counter}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $spreadsheet->getActiveSheet()->setSelectedCells('A1');
            // export XLSX file for download
            $this->ExportFile->export($spreadsheet, $config['filename']);
            exit;
        } catch (Exception $e) {
            Log::write('debug', $e);
            $this->Flash->error($this->errorMessage['error']);
            return $this->redirect(['action' => 'index']);
        }
    }


    /**
     * Delete method
     *
     * @param string|null $id Installment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $installment = $this->Installments->get($id);
        if ($this->Installments->delete($installment)) {
            $this->Flash->success($this->successMessage['deleteNoName']);
        } else {
            $this->Flash->error($this->errorMessage['error']);
        }
        return $this->redirect(['action' => 'index']);
    }

    public function deleteFees($id = null)
    {
        $this->request->allowMethod('ajax');

        $resp = [
            'status' => 'error',
            'alert' => [
                'title' => 'Lỗi',
                'type' => 'error',
                'message' => $this->errorMessage['error']
            ]
        ];
        try {
            $fees = $this->Installments->InstallmentFees->get($id);
            Log::write('debug', $fees);
            if ($this->Installments->InstallmentFees->delete($fees)) {
                $resp = [
                    'status' => 'success',
                    'alert' => [
                        'title' => 'Thành Công',
                        'type' => 'success',
                        'message' => $this->successMessage['deleteNoName']
                    ]
                ];
            }
        } catch (Exception $e) {
            //TODO: blacklist user
            Log::write('debug', $e);
        }
        return $this->jsonResponse($resp);
    }
}