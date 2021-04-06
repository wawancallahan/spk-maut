<?php
    $type = $_SESSION['type'] ?? null;
    $message = $_SESSION['message'] ?? '';
?>

<?php if ($type !== null) { ?>
    <div class="alert alert-<?php echo $type ?> alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo $message ?>
    </div>
<?php } ?>