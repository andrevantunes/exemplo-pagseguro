<?php
require_once 'helpers.php';
require_once 'PagSeguroAdapter.php';

$result = (new PagSeguroAdapter())->findTransactions($_GET['id']);
pre($result->code, false);
pre($result->status, false);
pre($result);
?>
<div>
    Status: <?="[[$result->status]]"?>, <?=PagSeguroAdapter::statusDetail($result->status)?>
</div>
<?php pre($result);?>