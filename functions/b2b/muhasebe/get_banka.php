<?php
include_once '../../db.php';

if (isset($_POST['bankaID'])) {
    $database = new Database();
    $bankaID = $_POST['bankaID'];
    $data = $database->fetchAll("SELECT * FROM b2b_banka_taksit_eslesme WHERE kart_id = :id ORDER BY taksit ASC", array('id' => $bankaID));
    foreach ($data as $row) {
        ?>
        <input type="text" id="kart_id" name="kart_id" hidden value="<?= $row['kart_id']; ?>" >
        <tr class="border-0">
            <td>[<?= $row['taksit']; ?>]</td>
            <td><?php
                switch ($row['pos_id']) {
                    case 1:
                        echo "Param Pos";
                        break;
                    case 2:
                        echo "Garanti Pos";
                        break;
                    case 3:
                        echo "Kuveyt Pos";
                        break;
                    case 4:
                        echo "Finans Pos";
                        break;
                    default:
                        echo "Boş";
                }
                ?>
            </td>

            <td><?= $row['vade']; ?></td>
            <td><?php 
                    $ticProg = $row['ticari_program'];
                    $result = $database->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = :id ", array('id' => $ticProg));
                    if ($result) {
                        $yazi = $result['id'] . '-' . $result['BANKA_ADI'] . ' - ' . $result['TANIMI'] . ' - Taksit Sayısı: ' . $result['TAKSIT_SAYISI'];
                        echo $yazi;
                    }
                ?>
            </td>

            <td>
                <label class="switch switch-success">
                    <input type="checkbox" class="switch-input aktifPasifBanka" name="<?php echo  $row['id']; ?>" id="<?php echo  $row['id']; ?>"  data-id="<?php echo  $row['id']; ?>"  <?php echo ($row['aktif'] == 1 ? 'checked' : ''); ?> />
                    <span class="switch-toggle-slider">
                        <span class="switch-on"><i class="ti ti-check"></i></span>
                        <span class="switch-off"><i class="ti ti-x"></i></span>
                    </span>
                </label>
            </td>
            <td>
                <button type="button" name="bankaDuzenle" value="Düzenle" class="btn btn-sm btn-outline-dark edit-taksit" data-taksit-id="<?= $row['id']; ?>"><i class="far fa-edit"></i></button>
                <button type="button" class="btn btn-sm btn-outline-dark" onclick="dynamicSil(<?php echo  $row['id']; ?>, '', 'banka', 'Taksit Tanımı silindi.', 'admin/muhasebe/adminBankaKomisyonlari.php');"><i class='far fa-trash-alt'></i></button>
            </td>
        </tr>
        <?php
    }
}