<?php
    $type = $_SESSION['type'] ?? null;
    $message = $_SESSION['message'] ?? '';
?>

<?php if ($type !== null) { ?>
    <div class="alert alert-<?php echo $type ?>">
        <?php echo $message ?>
    </div>
<?php } ?>