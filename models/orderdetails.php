<?

/**
 *
 */
class OrderDetails extends model
{
    var $table = "orderdetails";

    function deleteSpareNotApprovedIssued($ticketId)
    {
        $sql = "delete orderdetails from orderdetails
    inner join orders where orders.id = orderdetails.orderid and
    orderdetails.pro_approval = 0 and orderdetails.pro_issued = 0 and
    orders.ticketid = '" . $ticketId . "'";
        //echo $sql;die();
        return executeQuery($sql);
    }

    function getList($orderid = "", $locationid = "", $productid = "", $order_status = "")
    {
        $sql = "select od.*,
                       l.name                                                as locationname,
                       b.name                                                as branchname,
                       p.id                                                  as productid,
                       p.name                                                as productname,
                       round(od.price * od.qty, 2)                           as excamount,
                       round(od.price * od.qty * (1 + od.vat_rate / 100), 2) as incamount,
                       round(od.price * od.qty * (od.vat_rate / 100), 2)     as vatamount,
                       case
                           when o.status = 'inactive' then 'canceled'
                           when o.sales_status = 'closed' then 'closed'
                           when current_date() > date_add(o.doc, INTERVAL o.validity_days day) then 'invalid'
                           else 'pending'
                           end                                               as order_status
                from orderdetails od
                         inner join orders o on od.orderid = o.id
                         inner join locations as l on l.id = o.locid
                         inner join branches b on l.branchid = b.id
                         inner join products p on od.productid = p.id
                where 1 = 1";
        if ($orderid) $sql .= " and o.id = $orderid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($productid) $sql .= " and p.id = $productid";

        $sql .= " having 1=1";
        if ($order_status) $sql .= " and order_status = '$order_status'";
//        debug($sql);
        return fetchRows($sql);
    }

    function getSpareDetails($ordernumber = '', $ticketid = "", $user = "", $dept = "", $filter = "", $jobcard = false)
    {
        $sql = "select od.ticketid,od.isfrom_jobcard,o.pro_approval,o.pro_issued,od.deptid,
    o.id,o.orderid as ordernumber,o.is_invoiced, o.qty, o.price, o.doc as orderdate, o.status as orderstatus,
    o.isallocated, s.name as serialname,s.invoiceno,p.id as prodid, p.name as productname,
    p.baseprice,p.barcode_office,p.barcode_manufacture,p.unit,p.point,p.description,
    p.trackserialno,c.vat_percent as vat_rate,u.name requestedby,ua.name as approvedbyname,
    ui.name as issuedbyname,st.name as ticket_status from orderdetails as o
    inner join orders as od on od.id = o.orderid
    inner join products as p on p.id = o.productid
    inner join categories as c on c.id = p.categoryid
    inner join users as u on u.id = o.createdby
    left join tickets as t on t.id = od.ticketid
    left join statuses as st on st.id = t.statusid
    left join users as ua on ua.id = o.pro_approvalby
    left join users as ui on ui.id = o.pro_issuedby
    left join serials as s on s.id = o.serialid where 1=1 and o.status = 'active'";

        if ($ordernumber) $sql .= " and o.orderid = " . $ordernumber;
        if ($ticketid) $sql .= " and od.ticketid = " . $ticketid;
        if ($user) $sql .= " and o.createdby = " . $user;
        if ($dept) $sql .= " and od.deptid = " . $dept;
        if ($jobcard) {
            $sql .= " and od.isfrom_jobcard = 1";
        } else {
            $sql .= " and od.isfrom_jobcard != 1";
        }
        if ($filter) $sql .= " and o.pro_approval = 1";
        //echo $sql; die();
        return fetchRows($sql);
    }

    function getApprovedSpare($ticketid = "")
    {
        $sql = "select o.isfrom_jobcard, o.ticketid,o.status as orderstatus,o.sales_status,
    od.id as orderDetId,od.pro_approval,od.pro_issued, od.is_invoiced from orders as o
    inner join orderdetails as od on od.orderid = o.id
    where od.pro_approval = 1 and od.pro_issued = 1 and od.is_invoiced = 0";
        if ($ticketid) $sql .= " and o.ticketid = " . $ticketid;
        //echo $sql;die();
        return fetchRows($sql);
    }
}
