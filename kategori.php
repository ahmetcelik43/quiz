
<?php
include "db.php";
include "function.php";
//$islem = isset($_GET["islem"]) ? addslashes(trim($_GET["islem"])) : null;
$jsonArray = array(); // array değişkenimiz bunu en alta json objesine çevireceğiz. 
$jsonArray["hata"] = FALSE; // Başlangıçta hata yok olarak kabul edelim. 

$_code = 200; // HTTP Ok olarak durumu kabul edelim. 

	
    // üye ekleme kısmı burada olacak. CREATE İşlemi 
 if($_SERVER['REQUEST_METHOD'] == "POST") {

	$gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.

    //$kullaniciAdi = ($_POST["kullaniciAdi"]);
    //$adSoyad =($_POST["adSoyad"]);
    //$sifre = ($_POST["sifre"]);
    //$posta = ($_POST["posta"]);
    //$telefon = addslashes($_POST["telefon"]);
    
    // Kontrollerimizi yapalım.
    // gelen kullanıcı adı veya e-posta veri tabanında kayıtlı mı kontrol edelim. 
   
    if(!isset($gelen_veri->ad) || empty($gelen_veri->ad) ) {
    	$_code = 400; 
		$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Boş Alan Bırakmayınız."; // Hatanın neden kaynaklı olduğu belirtilsin.
	}


   
	else if($db->query("SELECT * from categorys WHERE  ad='$gelen_veri->ad'")->rowCount() !=0)
	 {
    	  $_code = 400;
        $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Kategori Adı Mevcut"; 
	}
	else
	 {
    


			$ex = $db->prepare("insert into categorys(ad,createdAt) values(
			 :ad, 
			:createdAt, 
			 )");
			$ekle2 = $ex->execute(array(
			"ad" => $gelen_veri->ad,
			"createdAt" => date("d-m-Y H:i:s");,
			));
		if($ekle2) {
			$_code = 201;
			$jsonArray["mesaj"] = "Eklendi";
		}else {
			$_code = 400;
			 $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
       		 $jsonArray["hataMesaj"] = "Sistem Hatası.";
		}
	}
}
else if($_SERVER['REQUEST_METHOD'] == "PUT") {
     $gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
    	
    	// basitçe bi kontrol yaptık veriler varmı yokmu diye 
     if(	isset($gelen_veri->kategoriAd) && 
     		!empty($gelen_veri->kategoriAd) && 
        isset($gelen_veri->kategoriID) && 
     		!empty($gelen_veri->kategoriID) 
     
     	) {
     		
     		// veriler var ise güncelleme yapıyoruz.
				$q = $db->prepare("UPDATE categorys SET ad= :kategoriAd WHERE id= :kategoriID ");
			 	$update = $q->execute(array(
			 			"kategoriAd" => $gelen_veri->kategoriAd,
			 			"kategoriID" => $gelen_veri->kategoriID,

			 	));
			 	// güncelleme başarılı ise bilgi veriyoruz. 
			 	if($update) {
			 		$_code = 200;
			 		$jsonArray["mesaj"] = "Güncelleme Başarılı";
			 	}
			 	else {
			 		// güncelleme başarısız ise bilgi veriyoruz. 
			 		$_code = 400;
					$jsonArray["hata"] = TRUE;
		 			$jsonArray["hataMesaj"] = "Sistemsel Bir Hata Oluştu";
				}
		}else {
			// gerekli veriler eksik gelirse apiyi kulanacaklara hangi bilgileri istediğimizi bildirdik. 
			$_code = 400;
			$jsonArray["hata"] = TRUE;
	 		$jsonArray["hataMesaj"] = "kategori Adı ve Kategori ID Verilerini json olarak göndermediniz.";
		}
} else if($_SERVER['REQUEST_METHOD'] == "DELETE") {
     $query = parse_str($_SERVER['QUERY_STRING']);
    // üye silme işlemi burada olacak. DELETE işlemi 
    if(isset($query) && !empty(trim($query)) && isset($query["kategoriID"]) && !empty(trim($query["kategoriID"]))) {
		$kategoriID = intval(trim($query["kategoriID"]));
		$kategoriVarMi = $db->query("select * from categorys where id='$kategoriID'")->rowCount();
		if($kategoriVarMi) {
			
			$sil = $db->query("delete from categorys where id='$kategoriID'");
			if( $sil ) {
				$_code = 200;
				$jsonArray["mesaj"] = "Kategori Silindi.";
			}else {
				// silme başarısız ise bilgi veriyoruz. 
				$_code = 400;
				$jsonArray["hata"] = TRUE;
	 			$jsonArray["hataMesaj"] = "Sistemsel Bir Hata Oluştu";
			}
		}else {
			$_code = 400; 
			$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
    	$jsonArray["hataMesaj"] = "Geçersiz Kategori Id"; // Hatanın neden kaynaklı olduğu belirtilsin.
		}
	}else {
		$_code = 400;
		$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
    	$jsonArray["hataMesaj"] = "Lütfen kategori id  gönderin"; // Hatanın neden kaynaklı olduğu belirtilsin.
	}
} else if($_SERVER['REQUEST_METHOD'] == "GET") {

	
    // üye bilgisi listeleme burada olacak. GET işlemi 
	
		$query = $db->query("select * from categorys");
		
		if($query->rowCount()) {
			
			//$bilgiler = $db->query("select * from  uyeler where id='$user_id'")->fetch(PDO::FETCH_ASSOC);
			$bilgiler = $query->fetch(PDO::FETCH_ASSOC);
			$jsonArray["uye-bilgileri"] = $bilgiler;
			$jsonArray["durum"] = "Başarılı";

			$_code = 200;
			
		}else {
			$_code = 400;
			$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
    		$jsonArray["hataMesaj"] = "Üye bulunamadı"; // Hatanın neden kaynaklı olduğu belirtilsin.
		}
	}
	

}
else {
	$_code = 406;
	$jsonArray["hata"] = TRUE;
 	$jsonArray["hataMesaj"] = "Geçersiz method!";
}


SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
?>
