<?php
	$hasil_saw_query = "SELECT count(*) AS hasil FROM hasil_saw";
	$execute = $konek->query($hasil_saw_query);
	$hasil_saw = $execute->fetch_array(MYSQLI_ASSOC);

	if (intval($hasil_saw['hasil']) == 0) {

		$query = "SELECT bobot_kriteria.id_bobotkriteria  AS 'id_bobotkriteria', alternatif.idalternatif AS 'idalternatif', alternatif.nikalternatif AS 'nikalternatif', kriteria.id_kriteria AS 'idkriteria', kriteria.namaKriteria AS 'namakriteria', kriteria.sifat AS 'sifat', kriteria.nilai AS 'nilai', nilai_kriteria.id_nilaikriteria AS 'id_nilaikriteria', nilai_kriteria.nilai AS 'nilai_kriteria' FROM bobot_kriteria INNER JOIN nilai_kriteria ON nilai_kriteria.id_nilaikriteria = bobot_kriteria.id_nilaikriteria INNER JOIN kriteria ON kriteria.id_kriteria = bobot_kriteria.id_kriteria INNER JOIN alternatif ON alternatif.idalternatif = bobot_kriteria.idalternatif";
		$execute=$konek->query($query);

		$datahasil = [];

		while($data=$execute->fetch_array(MYSQLI_ASSOC)){

			if ( ! isset($datahasil[$data['idalternatif']])) {
				$datahasil[$data['idalternatif']] = [
					'id_bobotkriteria' => $data['id_bobotkriteria'],
					'nama' => $data['nikalternatif'],
					'total' => 0,
					'items' => []
				];

				$datahasil[$data['idalternatif']]['items'][$data['idkriteria']] = [
					'nama' => $data['namakriteria'],
					'kriteria' => $data['idkriteria'],
					'sifat' => $data['sifat'],
					'nilai' => $data['nilai_kriteria'],
					'nilainormalisasi' => 0,
					'nilaihasil' => 0,
				];	
			} else {
				$datahasil[$data['idalternatif']]['items'][$data['idkriteria']] = [
					'nama' => $data['namakriteria'],
					'kriteria' => $data['idkriteria'],
					'sifat' => $data['sifat'],
					'nilai' => $data['nilai_kriteria'],
					'nilainormalisasi' => 0,
					'nilaihasil' => 0
				];
			}	
		}

		$querykriteria = "SELECT * FROM kriteria";
		$execute = $konek->query($querykriteria);
		$datakriteria = [];
		while ($data = $execute->fetch_array(MYSQLI_ASSOC)) {
			$datakriteria[] = $data;
		}

		$totalbobotkriteria = 0;
		foreach ($datakriteria as $key => $kriteriaitem) {
			$totalbobotkriteria += $kriteriaitem['nilai'];
		}

		$normalisasikriteria = [];

		foreach ($datakriteria as $key => $kriteriaitem) {
			$nilainormalisasi = number_format($kriteriaitem['nilai'] / $totalbobotkriteria,	2);
			$normalisasikriteria[$kriteriaitem['id_kriteria']] = [
				'namakriteria' => $kriteriaitem['namaKriteria'],
				'sifat' => $kriteriaitem['sifat'],
				'nilai' => $kriteriaitem['nilai'],
				'nilainormalisasi' => $nilainormalisasi
			];
		}

		$matrikkeputusan = [];
		foreach ($datakriteria as $key => $kriteriaitem) {
			$matrikkeputusan[$kriteriaitem['id_kriteria']] = [
				'idkriteria' => $kriteriaitem['id_kriteria'],
				'sifat' => $kriteriaitem['sifat'],
				'nilai' => 0
			];
		}

		foreach ($matrikkeputusan as $matrikkeputusanitem) {
			if ($matrikkeputusanitem['sifat'] == 'Benefit') {
				$nilaimax = max(
					array_map(function ($item) use ($matrikkeputusanitem) {
						return $item['items'][$matrikkeputusanitem['idkriteria']]['nilai'];
					}, $datahasil)
				);
				$matrikkeputusan[$matrikkeputusanitem['idkriteria']]['nilai'] = $nilaimax;
			} else if ($matrikkeputusanitem['sifat'] == 'Cost') {
				$nilaimin = min(
					array_map(function ($item) use ($matrikkeputusanitem) {
						return $item['items'][$matrikkeputusanitem['idkriteria']]['nilai'];
					}, $datahasil)
				);
				$matrikkeputusan[$matrikkeputusanitem['idkriteria']]['nilai'] = $nilaimin;
			}
		}

		foreach ($datahasil as $idalternatif => $datahasilitem) {
			foreach ($datahasilitem['items'] as $datahasilkriteria) {
				$nilainormalisasi = number_format($datahasilkriteria['nilai'] / $matrikkeputusan[$datahasilkriteria['kriteria']]['nilai'], 2);
				$datahasil[$idalternatif]['items'][$datahasilkriteria['kriteria']]['nilainormalisasi'] = $nilainormalisasi;
			}
		}

		foreach ($datahasil as $idalternatif => $datahasilitem) {
			foreach ($datahasilitem['items'] as $datahasilkriteria) {
				$nilaihasil = number_format($datahasilkriteria['nilainormalisasi'] * $normalisasikriteria[$datahasilkriteria['kriteria']]['nilainormalisasi'], 2);

				$datahasil[$idalternatif]['items'][$datahasilkriteria['kriteria']]['nilaihasil'] = $nilaihasil;
			}
		}	

		foreach ($datahasil as $idalternatif => $datahasilitem) {
			$total = 0;
			foreach ($datahasilitem['items'] as $datahasilkriteria) {
				$total += $datahasilkriteria['nilaihasil'];
			}

			$datahasil[$idalternatif]['total'] = $total;
		}	


		$datahasilakhir = [];
		foreach ($datahasil as $idalternatif => $datahasilitem) {
			$datahasilakhir[$idalternatif] = $datahasilitem['total'];
		}	

		arsort($datahasilakhir);

		$no = 1;
        foreach ($datahasilakhir as $idalternatif => $datahasilitem) {
			$id_bobotkriteria = $datahasil[$idalternatif]['id_bobotkriteria'];
			$query_tambah_nilai = "INSERT INTO hasil_saw VALUES (null, $idalternatif, $id_bobotkriteria, $no, $datahasilitem)";
			$konek->query($query_tambah_nilai);

			$no++;
		}
	}

	$data_saw_query = "SELECT hasil_saw.nilai AS 'nilai', hasil_saw.rangking AS 'rangking', alternatif.nikalternatif AS 'nama' FROM hasil_saw INNER JOIN alternatif ON alternatif.idalternatif = hasil_saw.alternatif_id ORDER BY hasil_saw.rangking ASC";
	$execute_saw = $konek->query($data_saw_query);
?>

<form method="POST" action="./proses/ulanghasilsaw.php">
	<button type="submit" class="btn btn-light-green">Input Ulang Ke Database</button>
</form>

<table class="table">
	<thead>
		<th>Ranking</th>
		<th>Altenatif</th>
		<th>Nilai</th>
	</thead>
	<tbody>
		<?php
			while ($data_saw = $execute_saw->fetch_array(MYSQLI_ASSOC)) {
        ?>
        	<tr>
        		<td><?php echo $data_saw['rangking'] ?></td>
        		<td><?php echo $data_saw['nama'] ?></td>
        		<td><?php echo $data_saw['nilai'] ?></td>
        	</tr>
        <?php } ?>
	</tbody>
</table>