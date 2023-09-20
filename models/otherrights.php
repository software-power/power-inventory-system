<?


class OtherRights extends model
{
    //user
    public const add_user = "add_user";
    public const edit_user = "edit_user";
    public const reset_password = "reset_password";

    //roles
    public const edit_roles = "edit_roles";

    //target
    public const add_target = "add_target";
    public const edit_target = "edit_target";

    //client
    public const add_client = "add_client";
    public const edit_client = "edit_client";
    public const assign_royalty_card = "assign_royalty_card";
    public const make_client_reseller = "make_client_reseller";
    public const update_client_contact = "update_client_contact";
    public const view_all_client_ledger = "view_all_client_ledger";
    public const view_managing_client_ledger = "view_managing_client_ledger";
    public const view_client_purchase = "view_client_purchase";

    //supplier
    public const add_supplier = "add_supplier";
    public const edit_supplier = "edit_supplier";

    //lpo
    public const add_lpo = "add_lpo";
    public const edit_lpo = "edit_lpo";
    public const approve_lpo = "approve_lpo";
    public const approve_other_lpo = "approve_other_lpo";

    //grn
    public const add_grn = "add_grn";
    public const edit_grn = "edit_grn";
    public const approve_grn = "approve_grn";
    public const cancel_grn = "cancel_grn";
    public const approve_other_grn = "approve_other_grn";

    //transfer
    public const transfer_stock = "transfer_stock";
    public const view_all_transfer = "view_all_transfer";
    public const edit_transfer = "edit_transfer";
    public const approve_transfer = "approve_transfer";
    public const approve_other_transfer = "approve_other_transfer";

    //requisition
    public const add_requisition = "add_requisition";
    public const approve_requisition = "approve_requisition";
    public const approve_other_requisition = "approve_other_requisition";

    //expense
    public const issue_expense = "issue_expense";
    public const approve_expense = "approve_expense";
    public const cancel_expense = "cancel_expense";
    public const approve_other_expense = "approve_other_expense";

    //stock
    public const adjust_stock = "adjust_stock";
    public const view_branch_stock = "view_branch_stock";
    public const view_all_branch_stock = "view_all_branch_stock";
    public const manufacture_stock = "manufacture_stock";
    public const approve_manufacture = "approve_manufacture";

    //supplier payment
    public const pay_supplier = "pay_supplier";

    //sales
    public const edit_order = "edit_order";
    public const sale_other_order = "sale_other_order";
    public const create_proforma = "create_proforma";
    public const edit_proforma = "edit_proforma";
    public const hold_stock = "hold_stock";
    public const create_bill = "create_bill";
    public const edit_bill = "edit_bill";
    public const approve_credit = "approve_credit";
    public const receive_credit_payment = "receive_credit_payment";
    public const receive_advance = "receive_advance";
    public const cancel_sale = "cancel_sale";
    public const refiscalize_invoice = "refiscalize_invoice";
    public const issue_credit_note = "issue_credit_note";
    public const approve_credit_note = "approve_credit_note";
    public const approve_other_credit_invoice = "approve_other_credit_invoice";
    public const approve_other_credit_note = "approve_other_credit_note";
    public const cancel_receipt = "cancel_receipt";
    public const resend_efd_receipt = "resend_efd_receipt";


    //product
    public const add_product = "add_product";
    public const edit_product = "edit_product";
    public const edit_price = "edit_price";
    public const admin_view = "admin_view";
    public const upload_product_image = "upload_product_image";
    public const view_all_location_stock = "view_all_location_stock";
    public const print_barcode = "print_barcode";
    public const edit_costprice = "edit_costprice";
    public const sale_discount = "sale_discount";
    public const sale_random_batch = "sale_random_batch";
    public const edit_reorder_level = "edit_reorder_level";


    var $table = 'other_rights';

    static $otherRightClass = null;

    function __construct()
    {
        self::$otherRightClass = $this;
    }
}