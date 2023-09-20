<?

class Clients extends model
{
    var $table = "clients";

    public static $clientClass = null;

    function __construct()
    {
        self::$clientClass = $this;
    }

    function search($name, $no_default = false)
    {
        $sql = "select name,
                       id
                from clients
                where status = 'active'";
        if ($name) $sql .= " and (name like '%" . escapeChar($name) . "%' or (tinno <> '' and tinno like '%" . escapeChar($name) . "%') or (vatno <> '' and vatno like '%" . escapeChar($name) . "%'))";
        if ($no_default) $sql .= " and id != 1";
        return fetchRows($sql);
    }

    function withRoyaltyCardInfo($id = "", $name = "", $no_default = false, $start_character = "",$mobile="")
    {
        $sql = "select clients.*,
					   manager.name     as account_manager,
					   rc.id            as cardId,
					   rc.name          as cardNo,
					   rc.assign_date,
					   rc.doc,
					   assigner.name    as assigner
				from clients
						 left join royalty_card rc on clients.id = rc.clientid
						 left join users assigner on rc.assignby = assigner.id
						 left join users manager on manager.id = clients.acc_mng
				where clients.status = 'active'";
        if ($id) {
            $sql .= " and clients.id = $id";
            return fetchRow($sql);
        }
        if ($name) $sql .= " and (clients.name like '%" . escapeChar($name) . "%' or rc.name like '%" . escapeChar($name) . "%' or (clients.tinno <> '' and clients.tinno like '%" . escapeChar($name) . "%') or (clients.vatno <> '' and clients.vatno like '%" . escapeChar($name) . "%'))";
        if ($mobile) $sql .= " and (clients.mobile <> '' and clients.mobile like '%$mobile%')";
        if ($no_default) $sql .= " and clients.id != 1";
        if ($start_character) {
            if ($start_character == '#') {
                $sql .= " and clients.name regexp '^[0-9]+'";
            } else {
                $sql .= " and clients.name like '" . escapeChar($start_character) . "%'";
            }
        }
//        debug($sql);
        return fetchRows($sql);
    }

    function withAccManager($clientid = "", $acc_mng = "", $status = 'active', $search = "")
    {
        $sql = "select clients.*,
					   manager.name     as account_manager
				from clients
						 left join users manager on manager.id = clients.acc_mng
				where clients.status = 'active'";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($acc_mng) $sql .= " and clients.acc_mng = $acc_mng";
        if ($status) $sql .= " and clients.status = '$status'";
        if ($search) $sql .= " and (clients.name like '%$search%' or (clients.tinno is not null and clients.tinno like '%$search%') or (clients.vatno is not null and clients.vatno like '%$search%'))";
        return fetchRows($sql);
    }

    function getClientContact($clientId = "", $search_contact = "", $search_client = "")
    {
        $sql = "select co.*,
                       c.name clientname,
                       c.tinno,
                       c.vatno
                from contacts co
                         left join clients c on c.id = co.id
                where 1 = 1";
        if ($clientId) $sql .= " and c.id = $clientId";
        if ($search_contact) $sql .= " and ((co.name <> '' and co.name like '%$search_contact%') or (co.email <> '' and co.email like '%$search_contact%') or (co.mobile <> '' and co.mobile like '%$search_contact%') or (co.position <> '' and co.position like '%$search_contact%'))";
        if ($search_client) $sql .= " and (c.name like '%$search_client%' or (c.tinno is not null and c.tinno like '%$search_client%') or (c.vatno is not null and c.vatno like '%$search_client%'))";
        return fetchRows($sql);
    }

    function history($clientid = "", $search = "", $issuedby = "", $fromdate = "", $todate = "")
    {
        $sales = $issuedby ? " and sales.createdby = $issuedby" : "";
        $sales .= $fromdate ? " and date_format(sales.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $sales .= $todate ? " and date_format(sales.doc,'%Y-%m-%d') <= '$todate'" : "";


        $orders = $issuedby ? " and orders.createdby = $issuedby" : "";
        $orders .= $fromdate ? " and date_format(orders.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $orders .= $todate ? " and date_format(orders.doc,'%Y-%m-%d') <= '$todate'" : "";

        $proformas = $issuedby ? " and proformas.createdby = $issuedby" : "";
        $proformas .= $fromdate ? " and date_format(proformas.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $proformas .= $todate ? " and date_format(proformas.doc,'%Y-%m-%d') <= '$todate'" : "";

        $salespayments = $issuedby ? " and salespayments.createdby = $issuedby" : "";
        $salespayments .= $fromdate ? " and date_format(salespayments.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $salespayments .= $todate ? " and date_format(salespayments.doc,'%Y-%m-%d') <= '$todate'" : "";

        $advance_payments = $issuedby ? " and advance_payments.createdby = $issuedby" : "";
        $advance_payments .= $fromdate ? " and date_format(advance_payments.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $advance_payments .= $todate ? " and date_format(advance_payments.doc,'%Y-%m-%d') <= '$todate'" : "";

        $credit_notes = $issuedby ? " and sr.createdby = $issuedby" : "";
        $credit_notes .= $fromdate ? " and date_format(sr.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $credit_notes .= $todate ? " and date_format(sr.doc,'%Y-%m-%d') <= '$todate'" : "";

        $sql = "select c.id,
                       c.name,
                       c.tinno,
                       c.vatno,
                       c.email,
                       c.mobile,
                       invoices.count       as invoicecount,
                       orders.count         as ordercount,
                       proformas.count      as proformacount,
                       sp.count             as receiptcount,
                       ap.count             as advancecount,
                       credit_notes.count   as creditnotecount
                from clients c
                         left join (select clientid, count(*) as count from sales where 1 = 1 and sales.iscreditapproved = 1 $sales group by clientid) as invoices on invoices.clientid = c.id
                         left join (select clientid, count(*) as count from orders where 1 = 1 $orders group by clientid) as orders on orders.clientid = c.id
                         left join (select clientid, count(*) as count from proformas where 1 = 1 $proformas group by clientid) as proformas on proformas.clientid = c.id
                         left join (select clientid, count(*) as count from salespayments where 1 = 1 $salespayments group by clientid) as sp on sp.clientid = c.id
                         left join (select clientid, count(*) as count from advance_payments where 1 = 1 $advance_payments group by clientid) as ap on ap.clientid = c.id
                         left join (select sales.clientid, count(*) as count from sales_returns sr inner join sales on sr.salesid = sales.id where 1 = 1 $credit_notes group by clientid) as credit_notes on credit_notes.clientid = c.id
                where 1 = 1";
        if ($clientid) $sql .= " and c.id = $clientid";
        if ($search) $sql .= " and (c.name like '%$search%' or (c.tinno is not null and c.tinno like '%$search%') or (c.vatno is not null and c.vatno like '%$search%'))";
        $sql .= " having invoicecount > 0 or ordercount > 0 or proformacount > 0 or receiptcount > 0 or advancecount > 0 or creditnotecount > 0";
//        debug($sql);
        return fetchRows($sql);
    }

    static function postToSupport($clientid, $support_mapping = [])
    {

        $result['status'] = 'success';

        $client = Clients::$clientClass->get($clientid);
        if (!$client) return ['status' => 'error', 'msg' => 'Client not found'];

        if (empty($support_mapping)) {
            $support_mapping[] = [
                'clientcode' => $clientid,
                'support_name' => CS_SUPPORT_NAME,
            ];
        }
        if (CS_MAIN_SYSTEM) { //main system send to support direct
            $data = [
                'name' => $client['name'],
                'industryid' => $client['industryid'],
                'tinno' => $client['tinno'],
                'vatno' => $client['vatno'],
                'codepath' => 'webinventory',
                'mobile' => $client['mobile'],
                'tel' => $client['tel'],
                'plotno' => $client['plotno'],
                'district' => $client['district'],
                'street' => $client['street'],
                'address' => $client['address'],
                'city' => $client['city'],
                'country' => $client['country'],
                'email' => $client['email'],
                'status' => $client['status'],
                'mapping' => $support_mapping,
                'industries' => [
                    ['id' => $client['industryid'], 'name' => Industries::$industryClass->get($client['industryid'])['name']]
                ],
            ];
            $data = json_encode($data);

            $response = sendSupportRequest(SUPPORT_ENDPOINT['clients'], $data, 'POST', true);
            if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
                $result['msg'] = "Sent to support";
            } else {
                $result['status'] = 'error';
                $result['msg'] = $response['msg'] ?: $response['data']['message'];
            }
        } else { //subsystem request main to send to support
            $sub_token = base64_encode(CS_SYSTEM_TOKEN); //subsystem system token
            $headers = [
                "Content-Type: application/json",
                "sub-token: $sub_token"
            ];

            if (empty($client)) return ['status' => 'error', 'msg' => 'Client code from main system not found!'];
            $data = json_encode([
                'client_maincode' => $client['code'],
                'support_mapping' => $support_mapping
            ]);
            $url = CS_MAIN_SYSTEM_URL . SystemTokens::REQUEST_MAIN_SEND_TO_SUPPORT;

            $response = sendHttpRequest($url, $data, 'POST', $headers);
            if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
                $result['msg'] = $response['data']['msg'];
            } else {
                $result['status'] = 'error';
                $result['msg'] = $result['msg'] = $response['msg'] ?: $response['data']['msg'];
            }
        }

        return $result;
    }

    static function mapToSupport($support_clientid, $support_mapping = [])
    {
        $result['status'] = 'success';
        if (empty($support_clientid)) return ['status' => 'error', 'msg' => 'Invalid support clint ID'];

        if (empty($support_mapping)) return ['status' => 'error', 'msg' => 'Invalid support mappings'];

        $data = [
            'support_clientid'=>$support_clientid,
            'support_name'=>CS_SUPPORT_NAME,
            'mapping' => $support_mapping,
        ];
//        debug($data);
        $data = json_encode($data);
        return sendSupportRequest(SUPPORT_ENDPOINT['clients_mapping'], $data, 'POST', true);
    }

    static function postToSubSystem($clientid, $selected_subsystems = []) //main system
    {
        $result['status'] = 'success';

        try {
            $client = Clients::$clientClass->get($clientid);
            if (!$client) throw new Exception('Client not found');


            $data = [
                'name' => $client['name'],
                'tinno' => $client['tinno'],
                'vatno' => $client['vatno'],
                'code' => $client['id'],
                'client_source' => 'main',
                'mobile' => $client['mobile'],
                'tel' => $client['tel'],
                'plotno' => $client['plotno'],
                'district' => $client['district'],
                'street' => $client['street'],
                'address' => $client['address'],
                'city' => $client['city'],
                'country' => $client['country'],
                'email' => $client['email'],
                'status' => $client['status'],
                'createdby' => $client['createdby'],
                'acc_mng_code' => $client['acc_mng'],
                'contacts' => array_map(function ($c) {
                    return [
                        'name' => $c['name'],
                        'email' => $c['email'],
                        'mobile' => $c['mobile'],
                        'position' => $c['position'],
                    ];
                }, Contacts::$contactClass->find(['clientid' => $clientid])),
            ];
            $data = json_encode($data);
            $subsystems = !empty($selected_subsystems)
                ? SystemTokens::$staticClass->findMany(['id' => $selected_subsystems])
                : SystemTokens::$staticClass->getAllActive();
            if (empty($subsystems)) throw new Exception('No subsystem selected');


            //get auth token

            $main_token = base64_encode(CS_SYSTEM_TOKEN); //main token because this is a main system
            $headers = [
                "Content-Type: application/json",
                "main-token: $main_token"
            ];

            foreach ($subsystems as $sub) {
                $url = $sub['endpoint'] . SystemTokens::SEND_CLIENT_URL;
                $response = sendHttpRequest($url, $data, 'POST', $headers);
                if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
                    $result['responses'][] = [
                        'status' => 'success',
                        'msg' => $sub['name'] . " => " . $response['data']['msg'],
                        'support_data' => $response['data']['support_data']
                    ];
                } else {
                    $result['responses'][] = [
                        'status' => 'error',
                        'msg' => $sub['name'] . " => " . ($response['msg'] ?: $response['data']['msg'])
                    ];
                }
            }

        } catch (Exception $e) {
            $result = ['status' => 'error', 'msg' => $e->getMessage()];
        }

        return $result;
    }


    static function getSubSupportCode($client_maincode, $selected_subsystems = [])
    {
        $result['status'] = 'success';
        try {
            $subsystems = !empty($selected_subsystems)
                ? SystemTokens::$staticClass->findMany(['id' => $selected_subsystems])
                : SystemTokens::$staticClass->getAllActive();
            if (empty($subsystems)) throw new Exception('No subsystem selected');

            //get auth token
            $main_token = base64_encode(CS_SYSTEM_TOKEN); //main token because this is a main system
            $headers = [
                "Content-Type: application/json",
                "main-token: $main_token"
            ];
            $data = json_encode(['client_maincode' => $client_maincode]);
            foreach ($subsystems as $sub) {
                $url = $sub['endpoint'] . SystemTokens::REQUEST_SUB_SUPPORT_CODE;
                $response = sendHttpRequest($url, $data, 'POST', $headers);
                if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
                    $result['responses'][] = [
                        'status' => 'success',
                        'msg' => $sub['name'],
                        'support_mapping' => $response['data']['support_mapping']
                    ];
                } else {
                    $result['responses'][] = [
                        'status' => 'error',
                        'msg' => $sub['name'] . " => " . ($response['msg'] ?: $response['data']['msg'])
                    ];
                }
            }
        } catch (Exception $e) {
            $result = ['status' => 'error', 'msg' => $e->getMessage()];
        }
        return $result;
    }
}
