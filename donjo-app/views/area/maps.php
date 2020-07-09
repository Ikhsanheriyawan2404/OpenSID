<?php
/**
 * File ini:
 *
 * View di modul Pemetaan
 *
 * /donjo-app/views/area/maps.php
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:

 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.

 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package OpenSID
 * @author  Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license http://www.gnu.org/licenses/gpl.html  GPL V3
 * @link  https://github.com/OpenSID/OpenSID
 */
?>

<style>
  #map
  {
    width:100%;
    height:65vh
  }
  .icon {
    max-width: 70%;
    max-height: 70%;
    margin: 4px;
  }
  .leaflet-control-layers {
  	display: block;
  	position: relative;
  }
  .leaflet-control-locate a {
  font-size: 2em;
	}
</style>
<!-- Menampilkan OpenStreetMap -->
<div class="content-wrapper">
  <section class="content-header">
		<h1>Peta <?= $area['nama']?></h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url('hom_sid')?>"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="<?= site_url("area")?>"> Pengaturan Area </a></li>
			<li class="active">Peta <?= $area['nama']?></li>
		</ol>
	</section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <form action="<?= $form_action?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
            <div class="box-body">
              <div class="row">
                <div class="col-sm-12">
                  <div id="map">
                    <input type="hidden" id="path" name="path" value="<?= $area['path']?>">
                    <input type="hidden" name="id" id="id"  value="<?= $area['id']?>"/>
                  </div>
                </div>
              </div>
            </div>
            <div class='box-footer'>
              <div class='col-xs-12'>
                <a href="<?= site_url("area")?>" class="btn btn-social btn-flat bg-purple btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Kembali"><i class="fa fa-arrow-circle-o-left"></i> Kembali</a>
                <a href="#" class="btn btn-social btn-flat btn-success btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" download="OpenSID.gpx" id="exportGPX"><i class='fa fa-download'></i> Export ke GPX</a>
								<button type='reset' class='btn btn-social btn-flat btn-danger btn-sm' id="resetme"><i class='fa fa-times'></i> Reset</button>
								<button type='submit' class='btn btn-social btn-flat btn-info btn-sm pull-right' id="simpan_kantor"><i class='fa fa-check'></i> Simpan</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  var infoWindow;
  window.onload = function()
  {
  	<?php if (!empty($desa['lat']) && !empty($desa['lng'])): ?>
  		var posisi = [<?=$desa['lat'].",".$desa['lng']?>];
  		var zoom = <?=$desa['zoom'] ?: 18?>;
  	<?php else: ?>
  		var posisi = [-1.0546279422758742,116.71875000000001];
  		var zoom = 4;
  	<?php endif; ?>

  	//Inisialisasi tampilan peta
  	var peta_area = L.map('map').setView(posisi, zoom);

    //1. Menampilkan overlayLayers Peta Semua Wilayah
    var marker_desa = [];
    var marker_dusun = [];
    var marker_rw = [];
    var marker_rt = [];
    var marker_area = [];
    var marker_garis = [];
    var marker_lokasi = [];
    var markers = new L.MarkerClusterGroup();
    var markersList = [];

    //OVERLAY WILAYAH DESA
    <?php if (!empty($desa['path'])): ?>
      set_marker_desa(marker_desa, <?=json_encode($desa)?>, "<?=ucwords($this->setting->sebutan_desa).' '.$desa['nama_desa']?>", "<?= favico_desa()?>");
    <?php endif; ?>

    //OVERLAY WILAYAH DUSUN
    <?php if (!empty($dusun_gis)): ?>
      set_marker(marker_dusun, '<?=addslashes(json_encode($dusun_gis))?>', '#FFFF00', '<?=ucwords($this->setting->sebutan_dusun)?>', 'dusun');
    <?php endif; ?>

    //OVERLAY WILAYAH RW
    <?php if (!empty($rw_gis)): ?>
      set_marker(marker_rw, '<?=addslashes(json_encode($rw_gis))?>', '#8888dd', 'RW', 'rw');
    <?php endif; ?>

    //OVERLAY WILAYAH RT
    <?php if (!empty($rt_gis)): ?>
      set_marker(marker_rt, '<?=addslashes(json_encode($rt_gis))?>', '#008000', 'RT', 'rt');
    <?php endif; ?>

    //Menampilkan overlayLayers Peta Semua Wilayah
    <?php if (!empty($wil_atas['path'])): ?>
      var overlayLayers = overlayWil(marker_desa, marker_dusun, marker_rw, marker_rt);
    <?php else: ?>
      var overlayLayers = {};
    <?php endif; ?>

    //Menampilkan BaseLayers Peta
    var baseLayers = getBaseLayers(peta_area, '<?=$this->setting->google_key?>');

    //Menampilkan Peta wilayah yg sudah ada
    <?php if (!empty($area['path'])): ?>
      var wilayah = <?=$area['path']?>;
      showCurrentArea(wilayah, peta_area);
    <?php endif; ?>

    //Menambahkan zoom scale ke peta
    L.control.scale().addTo(peta_area);

    //Menambahkan toolbar ke peta
    peta_area.pm.addControls(editToolbarPoly());

    //Menambahkan Peta wilayah
    addPetaPoly(peta_area);

    //Export/Import Peta dari file GPX
    L.Control.FileLayerLoad.LABEL = '<img class="icon" src="<?= base_url()?>assets/images/gpx.png" alt="file icon"/>';
    L.Control.FileLayerLoad.TITLE = 'Impor GPX/KML';
    control = eximGpxPoly(peta_area);

    //Import Peta dari file SHP
    eximShp(peta_area);

    //Geolocation IP Route/GPS
  	geoLocation(peta_area);

    //Menghapus Peta wilayah
    hapusPeta(peta_area);

    //Menampilkan OverLayer Area, Garis, Lokasi
    var layer_area = L.featureGroup();
    var layer_garis = L.featureGroup();
    var layer_lokasi = L.featureGroup();

    var layerCustom = {
      "Infrastruktur Desa": {
        "Infrastruktur (Area)": layer_area,
        "Infrastruktur (Garis)": layer_garis,
        "Infrastruktur (Lokasi)": layer_lokasi
      }
    };

    //OVERLAY AREA
    <?php if (!empty($all_area)): ?>
      set_marker_area(marker_area, '<?=addslashes(json_encode($all_area))?>');
    <?php endif; ?>

    //OVERLAY GARIS
    <?php if (!empty($all_garis)): ?>
      set_marker_garis(marker_garis, '<?=addslashes(json_encode($all_garis))?>');
    <?php endif; ?>

    //OVERLAY LOKASI DAN PROPERTI
    <?php if (!empty($all_lokasi)): ?>
      set_marker_lokasi(marker_lokasi, '<?=addslashes(json_encode($all_lokasi))?>', '<?= base_url()."assets/images/gis/point/"?>');
    <?php endif; ?>

    setMarkerCustom(marker_area, layer_area);
    setMarkerCustom(marker_garis, layer_garis);
    setMarkerCluster(marker_lokasi, markersList, markers);

    peta_area.on('layeradd layerremove', function () {
      var bounds = new L.LatLngBounds();
      peta_area.eachLayer(function (layer) {
        if(peta_area.hasLayer(layer_lokasi)) {
          peta_area.addLayer(markers);
        } else {
          peta_area.removeLayer(markers);
        }
        if (layer instanceof L.FeatureGroup) {
          bounds.extend(layer.getBounds());
        }
      });
    });

    L.control.layers(baseLayers, overlayLayers, {position: 'topleft', collapsed: true}).addTo(peta_area);
    L.control.groupedLayers('', layerCustom, {groupCheckboxes: true, position: 'topleft', collapsed: true}).addTo(peta_area);

  }; //EOF window.onload
</script>
<script src="<?= base_url()?>assets/js/leaflet.filelayer.js"></script>
<script src="<?= base_url()?>assets/js/togeojson.js"></script>
