<?

class RoyaltyCard extends model
{
    var $table = "royalty_card";

    function maxid()
    {
        $sql = "SELECT MAX(id) AS maxID FROM royalty_card";
        return fetchRow($sql);
    }

    function cardList($cardNo = "", $clientid = "")
    {
        $sql = "select card.*,
                       clients.name as clientname,
                       users.name as creator
                from royalty_card card
                         left join clients on clients.id = card.clientid
                         inner join users on card.createdby = users.id
                         ";
        return fetchRows($sql);
    }

    function findUnAssigned($name)
    {
        $sql = "select * from royalty_card where clientid is null and name like '%$name%'";
//        debug($sql);
        return fetchRows($sql);
    }
}