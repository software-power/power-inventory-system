<?

require_once 'lib/spout-3.3.0/src/Spout/Autoloader/autoload.php';

function clearTempData($temp_import)
{
    $iterator = new RecursiveDirectoryIterator($temp_import, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }

    rmdir($temp_import);
}

if ($action == 'client_with_contacts') {
    Users::isAllowed();
    try {
        $temp_import = 'temp_import/';
        mkdir($temp_import);
        $file_path = $temp_import . 'client_export.xlsx';
        $handler = fopen($file_path, 'w');
        fwrite($handler, '');
        fclose($handler);
        $writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($file_path);

        //Client sheet
        $clientSheet = $writer->getCurrentSheet();
        $clientSheet->setName('Clients');

        //headers
        /** Create a style with the StyleBuilder */
        $header_style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->build();

        /** Create a row with cells and apply the style to all cells */
        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'Date Created', 'Date Modified', 'Client Name', 'Phone', 'Mobile', 'Email', 'City',
            'Name', 'Phone', 'Email', 'Position',
            'Name', 'Phone', 'Email', 'Position',
            'Name', 'Phone', 'Email', 'Position'

        ], $header_style);

        $writer->addRow($row);


        $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $writer->addRows(array_map(function ($c) use ($style) {
            $contacts = Contacts::$contactClass->find(['clientid' => $c['id']]);
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                fDate($c['doc'], 'd/M/Y'), $c['dom'] ? fDate($c['dom'], 'd/M/Y') : '', $c['name'], $c['tel'], $c['mobile'], $c['email'], $c['city'],
                $contacts[0]['name'], $contacts[0]['mobile'], $contacts[0]['email'], $contacts[0]['position'],
                $contacts[1]['name'], $contacts[1]['mobile'], $contacts[1]['email'], $contacts[1]['position'],
                $contacts[2]['name'], $contacts[2]['mobile'], $contacts[2]['email'], $contacts[2]['position'],
            ], $style);
        }, $Clients->getAllActive()));

        $writer->close();
        download_file($file_path, 'Clients with contacts.xlsx');

        clearTempData($temp_import);
    } catch (Exception $e) {
        $writer->close();
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'full_client_info') {
    Users::isAllowed();
    try {
        $temp_import = 'temp_import/';
        mkdir($temp_import);
        $file_path = $temp_import . 'full_info_client.xlsx';
        $handler = fopen($file_path, 'w');
        fwrite($handler, '');
        fclose($handler);
        $writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($file_path);

        //Client sheet
        $clientSheet = $writer->getCurrentSheet();
        $clientSheet->setName('Clients');

        //headers
        /** Create a style with the StyleBuilder */
        $header_style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->build();

        /** Create a row with cells and apply the style to all cells */
        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'clientid', 'code', 'name', 'tinno', 'vatno', 'address', 'city', 'district', 'street', 'mobileno', 'email',
            'contact1', 'email1', 'mobile1', 'position1',
            'contact2', 'email2', 'mobile2', 'position2',
            'contact3', 'email3', 'position3', 'mobile3',

        ], $header_style);

        $writer->addRow($row);


        $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $writer->addRows(array_map(function ($c) use ($style) {
            $contacts = Contacts::$contactClass->find(['clientid' => $c['id']]);
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                $c['id'], $c['code'], $c['name'], $c['tinno'], $c['vatno'], $c['address'], $c['city'], $c['district'], $c['street'], $c['mobile'], $c['email'],
                $contacts[0]['name'], $contacts[0]['mobile'], $contacts[0]['email'], $contacts[0]['position'],
                $contacts[1]['name'], $contacts[1]['mobile'], $contacts[1]['email'], $contacts[1]['position'],
                $contacts[2]['name'], $contacts[2]['mobile'], $contacts[2]['email'], $contacts[2]['position'],
            ], $style);
        }, $Clients->getAllActive()));

        $writer->close();
        download_file($file_path, 'Full Clients info with contacts.xlsx');

        clearTempData($temp_import);
    } catch (Exception $e) {
        $writer->close();
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'products') {
    Users::isAllowed();
    try {
        $temp_import = 'temp_import/';
        mkdir($temp_import);
        $file_path = $temp_import . 'all_products.xlsx';
        $handler = fopen($file_path, 'w');
        fwrite($handler, '');
        fclose($handler);
        $writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($file_path);

        //Client sheet
        $productsheet = $writer->getCurrentSheet();
        $productsheet->setName('Products');

        //headers
        /** Create a style with the StyleBuilder */
        $header_style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->build();

        /** Create a row with cells and apply the style to all cells */
        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'productid', 'name', 'Generic name', 'Description', 'Created at', 'Non Stock', 'Department', 'Brand', 'Vat%', 'Category', 'Subcategory',
            'Unit', 'Bulk unit', 'Manufacture barcode', 'Other barcode', 'Status', 'Track Expire', 'Notify before days', 'Require Prescription'
        ], $header_style);

        $writer->addRow($row);


        $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $writer->addRows(array_map(function ($p) use ($style) {
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                $p['id'], $p['name'], $p['generic_name'], $p['description'], $p['doc'], $p['non_stock'] ? 'Yes' : 'No', $p['departmentName'], $p['brandName'], $p['categoryName'] . " " . $p['vatPercent'] . "%",
                $p['productcategory'], $p['productsubcategory'], $p['unitname'], $p['bulk_unit_name'], $p['barcode_manufacture'], $p['barcode_office'], $p['status'], $p['track_expire_date'] ? 'Yes' : 'No',
                $p['notify_before_days'], $p['prescription_required'] ? 'Yes' : 'No',
            ], $style);
        }, Products::$productClass->getList()));

        $writer->close();
        download_file($file_path, 'All Products.xlsx');

        clearTempData($temp_import);
    } catch (Exception $e) {
        $writer->close();
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}
