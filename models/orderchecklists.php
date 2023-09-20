<?

class OrderChecklists extends model
{
    var $table = "order_checklists";

    function getOrderChecklist($ordernumber = '')
    {

    }

    function getList($orderid)
    {
        $orderid = removeSpecialCharacters($orderid);
        $sql = "select cl.id,
                       cl.name,
                       oc.id      as ocid,
                       oc.orderid,
                       oc.remark,
                       oc.createdby,
                       oc.doc,
                       oc.file_path,
                       users.name as username
                from checklists cl
                         left join order_checklists oc on cl.id = oc.cid and oc.orderid = $orderid
                         left join users on oc.createdby = users.id
                where 1 = 1";
        return fetchRows($sql);
    }
}
