<section>
    <h1><?= $judul ?></h1>
    
    <p align='justify'>Pada pengertian codeigniter di atas tadi di jelaskan bahwa codeigniter menggunakan metode **MVC**. Apa itu MVC? Kita juga harus mengetahui apa itu MVC sebelum masuk dan lebih jauh dalam belajar codeigniter.</p>
    
    <p>MVC adalah teknik atau konsep yang memisahkan komponen utama menjadi tiga komponen yaitu **model**, **view** dan **controller**.</p>
    
    <ol type="a">
        <li>Model</li>
        <p align='justify'>Model adalah kelas yang merepresentasikan atau memodelkan tipe data yang akan digunakan oleh aplikasi. Model juga dapat didefinisakn sebagai bagian penanganan yang berhubungan dengan pengolahan atau manipulasi database. Semua intruksi atau fungsi yang berhubung dengan pengolahan database di letakkan di dalam model.</p>
        
        <p align='justify'>Sebagai catatan, Semua model harus disimpan di dalam folder **`application\models`**.</p>
        
        <li>View</li>
        <p align='justify'>View merupakan bagian yang menangani halaman user interface atau halaman yang muncul pada user (pada browser). Tampilan dari user interface di kumpulkan pada view untuk memisahkannya dengan controller dan model sehingga memudahkan web designer dalam melakukan pengembangan tampilan halaman website.</p>
        
        <li>Controller</li>
        <p align='justify'>Controller merupakan kumpulan intruksi aksi yang menghubungkan model dan view, jadi user tidak akan berhubungan dengan model secara langsung, intinya data yang tersimpan di database (model) di ambil oleh controller dan kemudian controller pula yang menampilkan nya ke view. Jadi controller lah yang mengolah intruksi.</p>
        
        <p align='justify'>Dari penjelasan tentang model view dan controller di atas dapat di simpulkan bahwa controller sebagai penghubung view dan model. Jadi jelas sudah dan sangat mudah dalam pengembangan aplikasi dengan cara mvc ini.</p>
    </ol>
</section>