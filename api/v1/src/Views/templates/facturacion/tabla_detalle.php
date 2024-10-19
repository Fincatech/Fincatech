<table class="table">
    <thead>
        <tr>
            <th>Concepto</th>
            <th class="text-right">Importe</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($data); $i++): ?>
        <tr>
            <td><?php echo $data[$i]['detail'];?></td>
            <td class="text-right"><?php echo number_format($data[$i]['total_taxes_exc'],2, ',','.');?>&euro;</td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>