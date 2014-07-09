<?php

defined('_JEXEC') or die;

/* Das Null-Datum der Datenbank, als Vergleichswert */
$nullDate = JFactory::getDbo()->getNullDate();
?>

<h1><?php echo'Wir verleihen viele Dinge!'; ?></h1>
<?php if ($this->items) { ?>
<table>
<tr>
    <th style="background: #ccc;">
        <?php echo 'Bezeichnung'; ?>
    </th>
    <th style="background: #ccc;">
        <?php echo 'Kategorie'; ?>
    </th>
    <th style="background: #ccc;">
        <?php echo 'Ausgeliehen an'; ?>
    </th>
    
</tr>
<?php foreach ($this->items as $item) : ?>
<tr>
    <td>
    
    
    </td>
    <td>
        <?php echo $item->name; ?>
    </td>
    <td>
        <?php echo $item->nachname; ?>
    </td>
    
</tr>
<?php endforeach;?>
</table>
<?php } ?>
