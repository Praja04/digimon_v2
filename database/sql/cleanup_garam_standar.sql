-- Hapus parameter lama (pH, Brix, %Kadar Air, %Kotoran milik Gula Tebu lama) yang masih menempel di id_jenis_bahan = 1 (Garam Halus)
DELETE FROM `master_standar_rms` 
WHERE `id_jenis_bahan` = 1 
  AND `id` NOT IN (33, 34, 35, 36, 37, 38, 39, 40);

