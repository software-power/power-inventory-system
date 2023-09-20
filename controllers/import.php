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

//products
if ($action == 'products') {
    if (isset($_GET['upload_result'])) $tData['upload_result'] = json_decode(base64_decode($_GET['upload_result']));
    $data['content'] = loadTemplate('import_products.tpl.php', $tData);
}

if ($action == 'download_product_template') {
    download_file('templates/product_template.xlsx', 'product upload template.xlsx');
}

if ($action == 'upload_products') {
    set_time_limit(0);
    $result = [];
    $temp_import = 'temp_import/';
    $file_path = '';
    $pattern = "/['\"?<>]/";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//    debug($_FILES);
        if (!$_FILES['excel_file']) throw new Exception("No file uploaded");


        $allowed_extension = array('xlsx');
        $file_array = explode(".", $_FILES["excel_file"]["name"]);
        $file_extension = end($file_array);
        if (!in_array($file_extension, $allowed_extension)) throw new Exception("Upload excel   *.xlsx file only");

        if (!is_dir($temp_import)) mkdir($temp_import);
        $file_path = $temp_import . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

        $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($file_path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($file_path);

        //departments
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'departments') {
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) continue;
                    $cells = $row->toArray();
                    $departmentname = $cells[0];
                    if (strlen(trim($departmentname)) == 0) throw new Exception("Enter valid department name in departments worksheet row number $rowno");
                    if (preg_match($pattern, $departmentname)) throw new Exception("Special character found on department \n\n$departmentname\n\n in departments worksheet row number $rowno");
                    if ($Departments->find(['name' => $departmentname])) {
                        $result['department']['exists']++;
                    } else {
                        $Departments->insert(['name' => $departmentname, 'createdby' => $_SESSION['member']['id'],]);
                        $result['department']['added']++;
                    }
                }
            }
        }

        //brands
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'brands') {
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) continue;
                    $cells = $row->toArray();
                    $brandname = $cells[0];
                    if (strlen(trim($brandname)) == 0) throw new Exception("Enter valid brand name in brands worksheet row number $rowno");
                    if (preg_match($pattern, $brandname)) throw new Exception("Special character found on brand \n\n$brandname\n\n in brands worksheet row number $rowno");
                    if ($Models->find(['name' => $brandname])) {
                        $result['brand']['exists']++;
                    } else {
                        $Models->insert(['name' => $brandname, 'createdby' => $_SESSION['member']['id'],]);
                        $result['brand']['added']++;
                    }
                }
            }
        }

        //units
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'units') {
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) continue;
                    $cells = $row->toArray();
                    $unitname = $cells[0];
                    $abbr = $cells[1];
                    if (strlen(trim($unitname)) == 0) throw new Exception("Enter valid unit name in units worksheet row number $rowno");
                    if (strlen(trim($abbr)) == 0) throw new Exception("Enter valid unit abbreviation in units worksheet row number $rowno");
                    if (preg_match($pattern, $unitname)) throw new Exception("Special character found on unit \n\n$unitname\n\n in units worksheet row number $rowno");
                    if (preg_match($pattern, $abbr)) throw new Exception("Special character found on unit abbreviation \n\n$abbr\n\n in units worksheet row number $rowno");
                    if ($Units->find(['name' => $unitname])) {
                        $result['unit']['exists']++;
                    } else {
                        $Units->insert(['name' => $unitname, 'abbr' => $abbr]);
                        $result['unit']['added']++;
                    }
                }
            }
        }

        //categories
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'categories') {
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) continue;
                    $cells = $row->toArray();
                    $categoryname = $cells[0];
                    if (strlen(trim($categoryname)) == 0) throw new Exception("Enter valid category name in categories worksheet row number $rowno");
                    if (preg_match($pattern, $categoryname)) throw new Exception("Special character found on category \n\n$categoryname\n\n in categories worksheet row number $rowno");
                    if ($ProductCategories->find(['name' => $categoryname])) {
                        $result['category']['exists']++;
                    } else {
                        $ProductCategories->insert(['name' => $categoryname, 'createdby' => $_SESSION['member']['id'],]);
                        $result['category']['added']++;
                    }
                }
            }
        }

        //subcategories
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'subcategories') {
                $categories = [];
                $arranged_categories = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $categories = $row->toArray();
                        foreach ($categories as $colindex => $categoryname) { //check if used category exists
                            $categoryid = $ProductCategories->find(['name' => $categoryname])[0]['id'];
                            if (!$categoryid)
                                throw new Exception("Category $categoryname was used in subcategories worksheet,\nwhile it does not exist in categories worksheet");
                            $arranged_categories[$colindex] = [
                                'categoryid' => $categoryid,
                                'categoryname' => $categoryname
                            ];
                        }
                        unset($categories);
                    } else {
                        foreach ($row->toArray() as $colindex => $subcategoryname) {
                            if (strlen(trim($subcategoryname)) == 0) continue;//skip empty cell
                            if (preg_match($pattern, $subcategoryname)) throw new Exception("Special character found on subcategory \n\n$subcategoryname\n\n in subcategories worksheet row number $rowno");

                            if ($ProductSubCategories->find(['name' => $subcategoryname, 'category_id' => $arranged_categories[$colindex]['categoryid']])) {
                                $result['subcategory']['exists']++;
                            } else {
                                $ProductSubCategories->insert(['name' => $subcategoryname, 'category_id' => $arranged_categories[$colindex]['categoryid'], 'createdby' => $_SESSION['member']['id']]);
                                $result['subcategory']['added']++;
                            }
                        }
                    }
                }
            }
        }

        //products
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'products') {
                $headers = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $p = array_combine($headers, $row->toArray());
//                        if ($rowno == 17) debug($p);
                        if (strlen(trim($p['productname'])) == 0) throw new Exception("Enter valid product name in products worksheet row number $rowno");
                        if (preg_match($pattern, $p['productname'])) throw new Exception("Special character found on product name \n\n{$p['productname']}\n\n in products worksheet row number $rowno");
                        if ($p['generic_name'] && preg_match($pattern, $p['generic_name'])) throw new Exception("Special character found on product generic name \n\n{$p['generic_name']}\n\n in products worksheet row number $rowno");
                        if ($p['description'] && preg_match($pattern, $p['description'])) throw new Exception("Special character found on product description \n\n{$p['description']}\n\n in products worksheet row number $rowno");

                        if ($Products->find(['name' => $p['productname']])) {
                            throw new Exception("Duplicate product name in products worksheet row number $rowno {$p['productname']}");
                        } else {

                            if (!($categoryid = $Categories->find(['name' => $p['tax_category']])[0]['id'])) throw new Exception("Invalid tax category in products worksheet row number $rowno");
                            if (!($departmentid = $Departments->find(['name' => $p['department_name']])[0]['id'])) throw new Exception("Department name not found in products worksheet row number $rowno");
                            if (!($brandid = $Models->find(['name' => $p['brand_name']])[0]['id'])) throw new Exception("Brand name not found in products worksheet row number $rowno");
                            if (!($productcategoryid = $ProductCategories->find(['name' => $p['category']])[0]['id'])) throw new Exception("Category ({$p['category']}) not found in products worksheet row number $rowno");
                            if (!($subcategoryid = $ProductSubCategories->find(['name' => $p['subcategory'], 'category_id' => $productcategoryid])[0]['id'])) throw new Exception("Subcategory ({$p['subcategory']}) not found for category ({$p['category']}) in products worksheet row number $rowno");
                            if (!($unitid = $Units->find(['name' => $p['unit_name']])[0]['id'])) throw new Exception("Unit name not found in products worksheet row number $rowno");
                            if ($p['barcode'] && $Products->byBarcode($p['barcode'], false)) throw new Exception("Barcode already exists, in products worksheet row number $rowno");
                            if ($p['other_barcode'] && $Products->byBarcode($p['other_barcode'], false)) throw new Exception("Other barcode already exists, in products worksheet row number $rowno");

                            if ($p['non_stock'] && ($p['track_serialno'] || $p['track_expire_date'] || $p['reorder_level'])) throw new Exception("Non stock item should not track serialno, expire date or reorder level \n, in products worksheet row number $rowno");
                            if ($p['track_serialno'] && $p['track_expire_date']) throw new Exception("Item can not track both expire date and serial no!\n, in products worksheet row number $rowno");

                            $Products->insert([
                                'name' => $p['productname'],
                                'generic_name' => $p['generic_name'] ?: '',
                                'description' => $p['description'],
                                'points' => $p['product_point'],
                                'categoryid' => $categoryid,
                                'departid' => $departmentid,
                                'unit' => $unitid,
                                'modelid' => $brandid,
                                'productcategoryid' => $productcategoryid,
                                'subcategoryid' => $subcategoryid,
                                'baseprice' => $p['base_percentage'] ?: CS_DEFAULT_BASE,
                                'barcode_manufacture' => $p['barcode'],
                                'barcode_office' => $p['other_barcode'] ?: Products::generateBarcode(),
                                'non_stock' => $p['non_stock'] ? 1 : 0,
                                'trackserialno' => $p['track_serialno'] ? 1 : 0,
                                'validate_serialno' => $p['validate_serialno'] ? 1 : 0,
                                'track_expire_date' => $p['track_expire_date'] ? 1 : 0,
                                'expiry_notification' => $p['get_expiry_notification'] ? 1 : 0,
                                'notify_before_days' => $p['notify_before_days'] ? 1 : 0,
                                'prescription_required' => $p['require_prescription'] ? 1 : 0,
                                'reorder_level' => $p['reorder_level'] ? 1 : 0,
                                'forquick_sale' => $p['show_in_quick_sale'] ? 1 : 0,
                                'createdby' => $_SESSION['member']['id'],
                                'doc' => TIMESTAMP,
                            ]);
                            $result['product']['added']++;
                        }
                    }
                }
            }
        }

        $reader->close();
//        debug($result);
        mysqli_commit($db_connection);
        clearTempData($temp_import);
        $_SESSION['message'] = "Upload finished successfully";
        redirect('import', 'products', ['upload_result' => base64_encode(json_encode($result))]);

    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        clearTempData($temp_import);
        $_SESSION['delay'] = 10000;
        $_SESSION['error'] = $e->getMessage();
//        debug(['Exception' => $e]);
        redirectBack();
    }

}

//stocks
if ($action == 'stocks') {
    $data['content'] = loadTemplate('import_stock.tpl.php', $tData);
}

if ($action == 'download_stock_template') {
    $with_products = isset($_GET['with_products']);

    if (!$with_products) {
        download_file('templates/stock_price_template.xlsx', 'stock upload template.xlsx');
    } else {
        try {
            $temp_import = 'temp_import/';
            mkdir($temp_import);
            $file_path = $temp_import . 'stock_import_products.xlsx';
            $handler = fopen($file_path, 'w');
            fwrite($handler, '');
            fclose($handler);
            $writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($file_path);

            //Stocks sheet
            $sheet = $writer->getCurrentSheet();
            $sheet->setName('Stocks');

            //headers
            /** Create a style with the StyleBuilder */
            $header_style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setFontBold()
                ->build();

            /** Create a row with cells and apply the style to all cells */
            $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                'product_id', 'other_barcode', 'product_name', 'stock_qty', 'cost_price', 'quick_price_inc'
            ], $header_style);

            $writer->addRow($row);

            $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                ->setShouldWrapText(false)
                ->build();

            $writer->addRows(array_map(function ($p) use ($style) {
                return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                    (string)$p['id'], (string)$p['barcode_office'], $p['name']
                ], $style);
            }, $Products->getList('', '', '', '', '', '', '', '', '', 'no', '', '', 'active')));

            //Batches sheet
            $batchSheet = $writer->addNewSheetAndMakeItCurrent();
            $batchSheet->setName('Batches');
            /** Create a row with cells and apply the style to all cells */
            $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                'product_id', 'batch_no', 'qty', 'expire_date'
            ], $header_style);

            $writer->addRow($row);


            $writer->close();
            download_file($file_path, 'stock upload template.xlsx');
            clearTempData($temp_import);
        } catch (Exception $e) {
            $writer->close();
            $_SESSION['error'] = $e->getMessage();
            redirectBack();
        }
    }
}

if ($action == 'upload_stocks') {
    set_time_limit(0);
    $result = [];
    $temp_import = 'temp_import/';
    $file_path = '';
    $pattern = "/['\"?<>]/";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $locationid = $_POST['locationid'];
        $location = $Locations->get($locationid);
        if (!$location) throw new Exception("location not found!");
        $branch = $Locations->getBranch($locationid);
        if (!$branch) throw new Exception("branch for this location not found!");
        if (!$_FILES['excel_file']) throw new Exception("No file uploaded");


        $allowed_extension = array('xlsx');
        $file_array = explode(".", $_FILES["excel_file"]["name"]);
        $file_extension = end($file_array);
        if (!in_array($file_extension, $allowed_extension)) throw new Exception("Upload excel   *.xlsx file only");

        if (!is_dir($temp_import)) mkdir($temp_import);
        $file_path = $temp_import . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

        $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($file_path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($file_path);


        //importing supplier
        $supplierid = $Suppliers->find(['name' => 'Importing Stock'])[0]['id'];
        if (!$supplierid) {

            $Suppliers->insert([
                'name' => 'Importing Stock',
                'contact_name' => 'Importing Stock',
                'contact_mobile' => '000000000',
                'status' => 'inactive',
                'createdby' => $_SESSION['member']['id'],
            ]);
            $supplierid = $Suppliers->lastId();
        }

        //opening GRN
        $GRN->insert([
            'locid' => $locationid,
            'supplierid' => $supplierid,
            'currency_rateid' => 1,
            'currency_amount' => 1,
            'total_amount' => 0,
            'grand_vatamount' => 0,
            'full_amount' => 0,
            'adjustment_amount' => 0,
            'paymenttype' => 'cash',
            'createdby' => $_SESSION['member']['id'],
            'token' => unique_token(),
        ]);
        $grnid = $GRN->lastId();


        $expirying_products = []; //products that wait batches from Batch sheet
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Stocks') {
                $headers = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
//                    if ($rowno == 329) break;
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $p = array_combine($headers, $row->toArray());
                        if (strlen(trim($p['product_id'])) == 0) throw new Exception("Enter valid product id in row number $rowno");
                        if (strlen(trim($p['stock_qty'])) == 0 || !is_int($p['stock_qty'])) throw new Exception("Enter valid stock quantity in row number $rowno");
                        if (strlen(trim($p['cost_price'])) == 0 || !is_numeric($p['cost_price'])) throw new Exception("Enter valid cost price in row number $rowno");
                        if (strlen(trim($p['quick_price_inc'])) == 0 || !is_numeric($p['quick_price_inc'])) throw new Exception("Enter valid quick price in row number $rowno");


                        $product = $Products->getList($p['product_id'])[0];
                        if ($product['track_expire_date']) {
                            $expirying_products[] = [
                                'id' => $product['id'],
                                'barcode' => $product['barcode_office']?:$product['barcode_manufacture'],
                                'name' => $product['name'],
                            ];
                        }
                        if (!$product) throw new Exception("Product not found in database in row number $rowno");
                        $stock = $Stocks->find(['productid' => $product['id'], 'locid' => $locationid]);
                        if ($stock) {//if there
                            $stockid = $stock[0]['id'];
                        } else {//if not there -> create the stock
                            $Stocks->insert(['productid' => $product['id'], 'locid' => $locationid, 'createdby' => $_SESSION['member']['id']]);
                            $stockid = $Stocks->lastId();
                        }
                        //details
                        $GRNDetails->insert([
                            'grnid' => $grnid,
                            'stockid' => $stockid,
                            'rate' => $p['cost_price'],
                            'quick_sale_price' => $p['quick_price_inc'],
                            'qty' => $p['stock_qty'],
                            'billable_qty' => 0,
                            'vat_percentage' => $product['vatPercent'],
                            'createdby' => $_SESSION['member']['id'],
                        ]);
                        $gid = $GRNDetails->lastId();
                        //batches
                        $batch_no = Batches::generateBatchNo($product['track_expire_date']);
                        $Batches->insert([
                            'batch_no' => $batch_no,
                            'qty' => $p['stock_qty'],
                            'gdi' => $gid,
                        ]);
                    }
                }
            }
        }

        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Batches') {
                $headers = [];
                $gdi_array = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $batch = array_combine($headers, $row->toArray());

                        if (strlen(trim($batch['product_id'])) == 0) throw new Exception("Enter valid product id in Batches sheet row number $rowno");
                        if (strlen(trim($batch['qty'])) == 0 || !is_int($batch['qty'])) throw new Exception("Enter valid quantity in Batches sheet row number $rowno");
                        if (!is_object($batch['expire_date'])) throw new Exception("Enter valid expire date in Batches sheet row number $rowno");
                        $batch['expire_date'] = $batch['expire_date']->format('Y-m-d');

                        $product = $Products->getList($batch['product_id'])[0];
                        if (!$product) throw new Exception("Product with id \n\n{$batch['product_id']}\n\n not found, Batches sheet row number $rowno");
//                        if (!$product['track_expire_date']) throw new Exception("Product with id \n\n{$batch['product_id']}\n\n does not track expire date, Batches sheet row number $rowno");

                        $grndetail = $GRNDetails->getList($grnid, $product['id'])[0];
                        if (!$grndetail) throw new Exception("Product with id \n\n{$batch['product_id']}\n\n not found in this stock import, Batches sheet row number $rowno");
                        $gdi = $grndetail['id'];
                        $batch['batch_no'] = $batch['batch_no'] ?: Batches::generateBatchNo($product['track_expire_date']);
                        unset($batch['product_id']);
                        $batch['gdi'] = $gdi;
                        if (!in_array($gdi, $gdi_array)) {
                            $gdi_array[] = $gdi;
                            $Batches->deleteWhere(['gdi' => $gdi]);//clear old batches
                            $Batches->insert($batch);//insert batchx
                            $GRNDetails->update($gdi, ['qty' => $batch['qty']]);
                        } else {
                            $GRNDetails->update($gdi, ['qty' => $grndetail['qty'] + $batch['qty']]);
                        }

                        //clear product in waiting list
                        $expirying_products = array_filter($expirying_products, function ($p) use ($product) {
                            return $product['id'] != $p['id'];
                        });
                    }
                }
            }
        }

        $reader->close();

        if(count($expirying_products)>0){
            try {
                $temp_import = 'temp_import/';
                mkdir($temp_import);
                $file_path1 = $temp_import . 'missing_batch_products.xlsx';
                $handler = fopen($file_path1, 'w');
                fwrite($handler, '');
                fclose($handler);
                $writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
                $writer->openToFile($file_path);

                //Stocks sheet
                $sheet = $writer->getCurrentSheet();
                $sheet->setName('Missing Batch');

                //headers
                /** Create a style with the StyleBuilder */
                $header_style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                    ->setFontBold()
                    ->build();

                /** Create a row with cells and apply the style to all cells */
                $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                    'product_id','barcode', 'product_name'
                ], $header_style);

                $writer->addRow($row);

                $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
                    ->setShouldWrapText(false)
                    ->build();

                $writer->addRows(array_map(function ($p) use ($style) {
                    return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                        (string)$p['id'], (string)$p['barcode'], $p['name']
                    ], $style);
                }, $expirying_products));

                $writer->close();
                download_file($file_path, 'products which miss batches.xlsx','',false);
                clearTempData($temp_import);
                throw new Exception("Product misses batches");
            } catch (Exception $e) {
                $writer->close();
                throw new Exception($e->getMessage());
            }
        }
//        debug($result);
        mysqli_commit($db_connection);
        clearTempData($temp_import);
        $_SESSION['message'] = "GRN from Importing Stock created successfully,waiting approval";

        redirect('grns', 'grn_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        clearTempData($temp_import);
        $_SESSION['delay'] = 10000;
        $_SESSION['error'] = $e->getMessage();
//        debug(['Exception' => $e]);
        redirectBack();
    }

}

//clients
if ($action == 'clients') {
    $data['content'] = loadTemplate('import_clients.tpl.php');
}

if ($action == 'download_client_template') {
    download_file('templates/client_template.xlsx', 'client upload template.xlsx');
}

if ($action == 'upload_clients1') {
    set_time_limit(0);
    $result = [];
    $temp_import = 'temp_import/';
    $file_path = '';
    $pattern = "/['\"?<>]/";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//    debug($_FILES);
        if (!$_FILES['excel_file']) throw new Exception("No file uploaded");


        $allowed_extension = array('xlsx');
        $file_array = explode(".", $_FILES["excel_file"]["name"]);
        $file_extension = end($file_array);
        if (!in_array($file_extension, $allowed_extension)) throw new Exception("Upload excel   *.xlsx file only");

        if (!is_dir($temp_import)) mkdir($temp_import);
        $file_path = $temp_import . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

        $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($file_path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($file_path);

        //clients
        if (!$Clients->get(1)) throw new Exception("Cash client must exist first before importing any client, Contact Support team for help");
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Clients') {
                $headers = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $c = array_combine($headers, $row->toArray());
                        if (strlen(trim($c['name'])) == 0) throw new Exception("Enter valid client name, row number $rowno");
                        if (preg_match($pattern, $c['name'])) throw new Exception("Client name contain special character, row number $rowno");
                        if (preg_match($pattern, $c['ledgername'])) throw new Exception("Ledger name contain special character, row number $rowno");
                        if ($Clients->find(['name' => $c['name']])) throw new Exception("Client name \n\n{$c['name']}\n\n already exists, row number $rowno");
                        if ($c['ledgername'] && $Clients->find(['ledgername' => $c['ledgername']])) throw new Exception("Ledger name \n\n{$c['ledgername']}\n\n already exists, row number $rowno");
                        if ($c['tinno'] && !is_numeric($c['tinno'])) throw new Exception("TIN number should contain number only, row number $rowno");
                        if ($c['tinno'] && strlen($c['tinno']) <> 9) throw new Exception("TIN should have 9 numbers in length, row number $rowno");
                        if ($c['tinno'] && $Clients->find(['tinno' => $c['tinno']])) throw new Exception("TIN number  \n\n{$c['tinno']}\n\n already exists, row number $rowno");
                        //clear special character
                        $c['plotno'] = removeSpecialCharacters($c['plotno']);
                        $c['district'] = removeSpecialCharacters($c['district']);
                        $c['street'] = removeSpecialCharacters($c['street']);
                        $c['address'] = removeSpecialCharacters($c['address']);
                        $c['city'] = removeSpecialCharacters($c['city']);
                        $c['country'] = removeSpecialCharacters($c['country']);
                        $c['email'] = preg_replace('/[^A-Za-z0-9@. ]/', '', $c['email']);
                        $c['country'] = removeSpecialCharacters($c['country']);
                        $c['country_code'] = removeSpecialCharacters($c['country_code']);
                        $c['mobile_country_code'] = $c['country_code'];
                        $c['createdby'] = $_SESSION['member']['id'];
                        $c['impno'] = $c['no'];
                        $c['acc_mng'] = $c['acc_manager'];
                        unset($c['no'], $c['acc_manager'], $c['country_code']);
                        $Clients->insert($c);
                    }
                }
            }
        }

        //contacts
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Contacts') {
                $headers = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $c = array_combine($headers, $row->toArray());
                        if (strlen(trim($c['name'])) == 0) throw new Exception("Enter valid contact name Contacts sheet row number $rowno");
                        if (strlen(trim($c['client_no'])) == 0 || $c['client_no'] == 0) throw new Exception("Enter valid client no, Contacts sheet row number $rowno");
                        $c['name'] = removeSpecialCharacters($c['name']);
                        $c['mobile'] = removeSpecialCharacters($c['mobile']);
                        $c['position'] = removeSpecialCharacters($c['position']);
                        $c['email'] = preg_replace('/[^A-Za-z0-9@. ]/', '', $c['email']);
                        $c['createdby'] = $_SESSION['member']['id'];
                        $client = $Clients->find(['impno' => $c['client_no']])[0];
                        if (!$client) throw new Exception("No client found with client no {$c['client_no']}, Contacts sheet row number $rowno");
                        if ($client['id'] == 1) throw new Exception("Client id {$client['id']} is reserved for System cash client, can not contain contacts, Contacts sheet row number $rowno");
                        unset($c['client_no']);
                        $c['clientid'] = $client['id'];
                        $Contacts->insert($c);
                    }
                }
            }
        }

        $Clients->updateWhere(['1' => 1], ['impno' => '']);

        $reader->close();
//        debug($result);
        mysqli_commit($db_connection);
        clearTempData($temp_import);
        $_SESSION['message'] = "Upload finished successfully";
        redirect('clients', 'client_index');

    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        clearTempData($temp_import);
        $_SESSION['delay'] = 10000;
        $_SESSION['error'] = $e->getMessage();
//        debug(['Exception' => $e]);
        redirectBack();
    }
}

if ($action == 'upload_clients') {
    set_time_limit(0);
    $result = [];
    $temp_import = 'temp_import/';
    $file_path = '';
    $pattern = "/['\"?<>]/";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//    debug($_FILES);
        if (!$_FILES['excel_file']) throw new Exception("No file uploaded");


        $allowed_extension = array('xlsx');
        $file_array = explode(".", $_FILES["excel_file"]["name"]);
        $file_extension = end($file_array);
        if (!in_array($file_extension, $allowed_extension)) throw new Exception("Upload excel   *.xlsx file only");

        if (!is_dir($temp_import)) mkdir($temp_import);
        $file_path = $temp_import . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

        $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($file_path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($file_path);

        //clients
        if (!$Clients->get(1)) throw new Exception("Cash client must exist first before importing any client, Contact Support team for help");
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Clients') {
                $headers = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $c = array_combine($headers, $row->toArray());
//                       if($rowno==17) debug([$headers,$row->toArray(),$c]);
                        if (strlen(trim($c['name'])) == 0) throw new Exception("Enter valid client name, row number $rowno");
                        if (preg_match($pattern, $c['name'])) throw new Exception("Client name contain special character, row number $rowno");
                        if ($Clients->find(['name' => $c['name']])) throw new Exception("Client name \n\n{$c['name']}\n\n already exists, row number $rowno");
                        if (strlen($c['tinno']) > 0 && !is_numeric($c['tinno'])) throw new Exception("TIN number should contain number only, row number $rowno");
                        if ($c['tinno'] && strlen($c['tinno']) <> 9) throw new Exception("TIN should have 9 numbers in length, row number $rowno");
//                        if ($c['tinno'] && $c['tinno'] != '999999999' && $Clients->find(['tinno' => $c['tinno']])) throw new Exception("TIN number  \n\n{$c['tinno']}\n\n already exists, row number $rowno");
                        //clear special character
                        $Clients->insert([
                            'name' => $c['name'],
                            'tinno' => $c['tinno'],
                            'vatno' => $c['vatno'],
                            'address' => removeSpecialCharacters($c['address']),
                            'city' => removeSpecialCharacters($c['city']),
                            'street' => removeSpecialCharacters($c['street']),
                            'mobile' => removeSpecialCharacters($c['mobileno']),
                            'email' => preg_replace('/[^A-Za-z0-9@. ]/', '', $c['email']),
                            'createdby' => $_SESSION['member']['id'],
                            'inventoryid' => $c['inventoryid'],
                        ]);

                        $clientid = $Clients->lastId();

                        $validContact = function (array $contact) {
                            return strlen(trim($contact['name'])) > 0
                                || strlen(trim($contact['mobile'])) > 0
                                || strlen(trim($contact['email'])) > 0
                                || strlen(trim($contact['position'])) > 0;
                        };

                        $contact = [
                            'name' => removeSpecialCharacters($c['contact1']),
                            'mobile' => removeSpecialCharacters($c['mobile1']),
                            'email' => preg_replace('/[^A-Za-z0-9@. ]/', '', $c['email1']),
                            'position' => removeSpecialCharacters($c['position1']),
                            'clientid' => $clientid,
                            'createdby' => $_SESSION['member']['id'],
                        ];
                        if ($validContact($contact)) $Contacts->insert($contact);

                        $contact = [];
                        $contact = [
                            'name' => removeSpecialCharacters($c['contact2']),
                            'mobile' => removeSpecialCharacters($c['mobile2']),
                            'email' => preg_replace('/[^A-Za-z0-9@. ]/', '', $c['email2']),
                            'position' => removeSpecialCharacters($c['position2']),
                            'clientid' => $clientid,
                            'createdby' => $_SESSION['member']['id'],
                        ];
                        if ($validContact($contact)) $Contacts->insert($contact);

                        $contact = [];
                        $contact = [
                            'name' => removeSpecialCharacters($c['contact3']),
                            'mobile' => removeSpecialCharacters($c['mobile3']),
                            'email' => preg_replace('/[^A-Za-z0-9@. ]/', '', $c['email3']),
                            'position' => removeSpecialCharacters($c['position3']),
                            'clientid' => $clientid,
                            'createdby' => $_SESSION['member']['id'],
                        ];
                        if ($validContact($contact)) $Contacts->insert($contact);
                    }
                }
            }
        }

        $reader->close();
//        debug($result);
        mysqli_commit($db_connection);
        clearTempData($temp_import);
        $_SESSION['message'] = "Upload finished successfully";
        redirect('clients', 'client_index');

    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        clearTempData($temp_import);
        $_SESSION['delay'] = 10000;
        $_SESSION['error'] = $e->getMessage();
//        debug(['Exception' => $e]);
        redirectBack();
    }
}


//sales outstandings
if ($action == 'sales_outstanding') {
    $data['content'] = loadTemplate('import_sale_outstandings.tpl.php');
}

if ($action == 'download_outstanding_template') {
    try {
        $temp_import = 'temp_import/';
        mkdir($temp_import);
        $file_path = $temp_import . 'sales_outstanding_template.xlsx';
        $handler = fopen($file_path, 'w');
        fwrite($handler, '');
        fclose($handler);
        $writer = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($file_path);

        //Outstanding sheet
        $outstandingSheet = $writer->getCurrentSheet();
        $outstandingSheet->setName('Outstandings');

        //headers
        /** Create a style with the StyleBuilder */
        $header_style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setFontBold()
            ->build();

        /** Create a row with cells and apply the style to all cells */
        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'invoiceno', 'client_id', 'location_id', 'currency_code', 'exchange_rate', 'outstanding_amount', 'invoice_date', 'salesperson', 'credit_days', 'description'

        ], $header_style);

        $writer->addRow($row);

        //Clients sheet
        $clientSheet = $writer->addNewSheetAndMakeItCurrent();
        $clientSheet->setName('Clients');

        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'client_id', 'client name', 'TIN'
        ], $header_style);

        $writer->addRow($row);

        $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $writer->addRows(array_map(function ($c) use ($style) {
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                $c['id'], $c['name'], $c['tinno']
            ], $style);
        }, $Clients->getAllActive()));

        //Locations sheet
        $locationSheet = $writer->addNewSheetAndMakeItCurrent();
        $locationSheet->setName('Locations');

        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'location_id', 'Location name',
        ], $header_style);

        $writer->addRow($row);

        $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $writer->addRows(array_map(function ($l) use ($style) {
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                $l['id'], $l['name'] . " - " . $l['branchname'],
            ], $style);
        }, $Locations->locationList()));

        //Currencies sheet
        $currencySheet = $writer->addNewSheetAndMakeItCurrent();
        $currencySheet->setName('Currencies');

        $row = \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
            'currency code', 'currency name', 'Is base currency'
        ], $header_style);

        $writer->addRow($row);

        $style = (new \Box\Spout\Writer\Common\Creator\Style\StyleBuilder())
            ->setShouldWrapText(false)
            ->build();

        $writer->addRows(array_map(function ($c) use ($style) {
            return \Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray([
                $c['name'], $c['description'], $c['base'] == 'yes' ? 'Yes' : ''
            ], $style);
        }, $Currencies->getAllActive()));


        $writer->close();
        download_file($file_path, 'Sales outstanding upload template.xlsx');

        clearTempData($temp_import);
    } catch (Exception $e) {
        $writer->close();
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'upload_outstandings') {
    set_time_limit(0);
    $result = [];
    $temp_import = 'temp_import/';
    $file_path = '';
    $pattern = "/['\"?<>]/";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//    debug($_FILES);
        if (!$_FILES['excel_file']) throw new Exception("No file uploaded");


        $allowed_extension = array('xlsx');
        $file_array = explode(".", $_FILES["excel_file"]["name"]);
        $file_extension = end($file_array);
        if (!in_array($file_extension, $allowed_extension)) throw new Exception("Upload excel   *.xlsx file only");

        if (!is_dir($temp_import)) mkdir($temp_import);
        $file_path = $temp_import . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

        $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($file_path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($file_path);

        //Outstandings
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Outstandings') {
                $headers = [];
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    if ($rowno == 1) {
                        $headers = $row->toArray();
                    } else {
                        $sale = array_combine($headers, $row->toArray());
//                       if($rowno==17) debug([$headers,$row->toArray(),$sale]);
                        if (strlen(trixm($sale['invoiceno'])) == 0) throw new Exception("Enter valid invoice number, row number $rowno");
                        if (preg_match($pattern, $sale['invoiceno'])) throw new Exception("Invoice number contain special character, row number $rowno");
                        if ($ClientOpeningOutstandings->find(['invoiceno' => $sale['invoiceno']])) throw new Exception("Invoice no \n\n{$sale['invoiceno']}\n\n already exists, row number $rowno");
                        if ($sale['client_id'] == 1) throw new Exception("Client id '1' is reserved for cash client, can not be used for outstanding sales, row number $rowno");
                        if (!$Clients->get($sale['client_id'])) throw new Exception("Client with id '{$sale['client_id']}' not found, row number $rowno");
                        if (!$Locations->get($sale['location_id'])) throw new Exception("Location with id '{$sale['location_id']}' not found, row number $rowno");
                        if (!($currencyid = $Currencies->find(['name' => $sale['currency_code']])[0]['id'])) throw new Exception("Currency with code '{$sale['currency_code']}' not found, row number $rowno");
                        if (!is_numeric($sale['exchange_rate'])) throw new Exception("Enter valid exchange rate, row number $rowno");
                        if (!is_numeric($sale['outstanding_amount'])) throw new Exception("Enter valid outstanding amount, row number $rowno");
                        if (is_object($sale['invoice_date'])) {
                            $sale['invoice_date'] = $sale['invoice_date']->format('Y-m-d');
                        }
                        if (strlen($sale['invoice_date']) == 0) throw new Exception("Enter valid invoice date, row number $rowno");
                        if (!is_numeric($sale['credit_days'])) throw new Exception("Enter valid credit days, row number $rowno");
                        $sale['salesperson'] = htmlspecialchars($sale['salesperson']);
                        if (!$Users->get($sale['salesperson'])) throw new Exception("Sales person not found, row number $rowno");
                        //client id 1 cant have outstanding invoices
                        //created by comes from upload

                        $ClientOpeningOutstandings->insert([
                            'invoiceno' => $sale['invoiceno'],
                            'currencyid' => $currencyid,
                            'currency_amount' => $sale['exchange_rate'],
                            'clientid' => $sale['client_id'],
                            'locationid' => $sale['location_id'],
                            'description' => removeSpecialCharacters($sale['description']),
                            'outstanding_amount' => $sale['outstanding_amount'],
                            'invoicedate' => $sale['invoice_date'],
                            'credit_days' => $sale['credit_days'],
                            'createdby' => $sale['salesperson'],
                        ]);
                    }
                }
            }
        }

        $reader->close();
//        debug($result);
        mysqli_commit($db_connection);
        clearTempData($temp_import);
        $_SESSION['message'] = "Upload finished successfully";
        redirect('sales', 'opening_outstanding');

    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        clearTempData($temp_import);
        $_SESSION['delay'] = 10000;
        $_SESSION['error'] = $e->getMessage();
//        debug(['Exception' => $e]);
        redirectBack();
    }
}


//account manager
if ($action == 'update_acc_mng') {
    $data['content'] = loadTemplate('update_acc_mng.tpl.php');
}

if ($action == 'update_clients_acc_mng') {
    Users::isAllowed();
    set_time_limit(0);
    $result = [];
    $temp_import = 'temp_import/';
    $file_path = '';
    $pattern = "/['\"?<>]/";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//    debug($_FILES);
        if (!$_FILES['excel_file']) throw new Exception("No file uploaded");


        $allowed_extension = array('xlsx');
        $file_array = explode(".", $_FILES["excel_file"]["name"]);
        $file_extension = end($file_array);
        if (!in_array($file_extension, $allowed_extension)) throw new Exception("Upload excel   *.xlsx file only");

        if (!is_dir($temp_import)) mkdir($temp_import);
        $file_path = $temp_import . time() . '.' . $file_extension;
        move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

        $reader = \Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createReaderFromFile($file_path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($file_path);

        //clients
        if (!$Clients->get(1)) throw new Exception("Cash client must exist first before importing any client, Contact Support team for help");
        foreach ($reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() == 'Sheet1') {
                $headers = [];
                $count = 0;
                foreach ($sheet->getRowIterator() as $rowno => $row) {
                    $row = $row->toArray();
                    $sql_query = $row[0];
                    if (!executeQuery($sql_query)) throw new Exception("Error execute query $rowno");
                    $count++;
                }
            }
        }

        $reader->close();
        mysqli_commit($db_connection);
        clearTempData($temp_import);
        debug($count);
        $_SESSION['message'] = "Upload finished successfully";
        redirect('clients', 'client_index');

    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        clearTempData($temp_import);
        $_SESSION['delay'] = 10000;
        $_SESSION['error'] = $e->getMessage();
//        debug(['Exception' => $e]);
        redirectBack();
    }
}
