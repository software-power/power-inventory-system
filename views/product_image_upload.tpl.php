<style>
    .input-error {
        border: 1px solid red;
    }
</style>
<header class="page-header">
    <h2>Upload Image</h2>
</header>
<div class="col-md-8 col-md-offset-2">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="panel-title">Upload Product Image</h2>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <h5>Product Name: <span class="text-primary"><?= $product['name'] ?></span></h5>
            <? if (CS_SHOW_GENERIC_NAME) { ?>
                <h5>Generic Name: <span class="text-primary"><?= $product['generic_name'] ?></span></h5>
            <? } ?>
            <p>Allowed format: <span class="text-danger">png, jpg, jpeg, gif</span></p>
            <div class="d-flex align-items-center flex-column">
                <div id="image-holder" title="Click to choose image"
                     style="height: 370px;width: 370px;border: 1px dashed grey;padding: 5px;border-radius: 10px;cursor: pointer;"
                     onclick="chooseImage()" class="d-flex justify-content-center align-items-center">
                    <img id="image-preview" src="<?= $product['image_path'] ?>" style="height: 350px;width: 350px;"
                         alt="choose image">
                </div>
                <form action="<?= url('products', 'save_image') ?>" method="post" enctype="multipart/form-data"
                      class="mt-md">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button id="upload-btn" class="btn btn-success btn-block" disabled>Upload</button>
                    <input id="file-input" type="file" name="image" class="form-control" style="visibility: hidden"
                           accept="image/*"
                           onchange="previewImage(this)">
                </form>
            </div>
        </div>
    </section>
</div>

<script>

    function chooseImage() {
        $('#file-input').trigger('click');
    }

    function previewImage(obj) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
        $('#upload-btn').prop('disabled', false);
    }
</script>
