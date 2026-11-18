<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Pertanian', 'description' => 'Tips budidaya, lahan, dan tanaman pangan.'],
            ['name' => 'Peternakan', 'description' => 'Peralatan dan manajemen peternakan.'],
            ['name' => 'Teknologi Pertanian', 'description' => 'IoT, drone, dan inovasi untuk lahan modern.'],
            ['name' => 'Informasi Umum', 'description' => 'Kabar dan layanan terbaru dari FunFarm.'],
        ])->mapWithKeys(function (array $data) {
            $category = ArticleCategory::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                $data
            );

            return [$category->slug => $category];
        });

        $articles = [
            [
                'category' => 'pertanian',
                'title' => 'Cara Menyiapkan Lahan Sawah Sebelum Musim Tanam',
                'excerpt' => 'Pengolahan tanah yang benar menentukan separuh keberhasilan panen. Berikut langkah menyiapkan lahan sawah, dari pembersihan jerami sampai penggenangan awal.',
                'days_ago' => 1,
                'content' => "Persiapan lahan adalah fondasi dari seluruh musim tanam. Tanah yang diolah dengan baik membuat akar padi mudah berkembang, air irigasi merata, dan gulma lebih terkendali sejak awal.\n\nLangkah pertama adalah membersihkan sisa jerami dan gulma dari petakan. Jerami dapat dibenamkan kembali ke tanah sebagai bahan organik, asalkan diberi waktu cukup untuk terurai sebelum bibit ditanam. Setelah bersih, lakukan pembajakan pertama untuk membalik tanah, lalu biarkan tergenang selama satu sampai dua minggu.\n\nBerikutnya adalah penggaruan untuk meratakan permukaan petakan. Permukaan yang rata membuat tinggi genangan air seragam di seluruh petakan, sehingga pertumbuhan tanaman lebih serentak. Pada tahap ini petani juga dapat memberikan pupuk dasar organik untuk memperbaiki struktur tanah.\n\nTerakhir, periksa kembali pematang dan saluran air. Pematang yang bocor membuat air dan pupuk terbuang percuma. Jika semua tahap ini dilakukan dengan urut, lahan siap menerima bibit dengan kondisi yang jauh lebih sehat.",
            ],
            [
                'category' => 'pertanian',
                'title' => 'Jadwal Pemupukan Padi yang Tepat untuk Hasil Maksimal',
                'excerpt' => 'Pemupukan padi bukan soal banyaknya pupuk, melainkan soal waktu. Kenali tiga fase pemupukan utama dan dosis yang dianjurkan untuk setiap fase pertumbuhan.',
                'days_ago' => 3,
                'content' => "Banyak petani mengira hasil panen ditentukan oleh jumlah pupuk yang ditebar. Padahal, waktu pemberian pupuk jauh lebih menentukan, karena kebutuhan unsur hara tanaman berubah pada setiap fase pertumbuhan.\n\nPemupukan pertama dilakukan pada umur 7 sampai 10 hari setelah tanam, dengan fokus pada nitrogen dan fosfor untuk merangsang pertumbuhan akar dan anakan awal. Pemupukan kedua pada umur 21 sampai 25 hari bertujuan memperbanyak anakan produktif, sedangkan pemupukan ketiga pada umur 35 sampai 40 hari mendukung pembentukan malai.\n\nDosis perlu disesuaikan dengan kondisi tanah dan varietas. Tanah yang miskin bahan organik biasanya membutuhkan tambahan pupuk organik di awal musim. Penggunaan bagan warna daun dapat membantu petani menilai apakah tanaman masih membutuhkan nitrogen tambahan atau sudah cukup.\n\nDengan jadwal yang disiplin, pupuk terserap lebih efisien, biaya produksi turun, dan risiko pencemaran air akibat pupuk berlebih dapat ditekan. Catat setiap pemberian pupuk di buku kegiatan tani agar musim berikutnya evaluasi menjadi lebih mudah.",
            ],
            [
                'category' => 'peternakan',
                'title' => 'Panduan Memilih Alat Penetas Telur untuk Pemula',
                'excerpt' => 'Mesin tetas yang tepat membuat tingkat keberhasilan penetasan jauh lebih tinggi. Perhatikan kapasitas, kestabilan suhu, dan sistem pemutaran telur sebelum membeli.',
                'days_ago' => 5,
                'content' => "Menetaskan telur dengan mesin memberi peternak kendali yang tidak dimiliki pada penetasan alami. Namun hasil yang baik sangat bergantung pada pemilihan alat yang sesuai dengan skala usaha.\n\nHal pertama yang perlu diperhatikan adalah kapasitas. Untuk pemula, mesin berkapasitas 50 sampai 100 butir sudah cukup untuk belajar tanpa risiko kerugian besar. Pastikan mesin memiliki termostat digital yang stabil, karena selisih suhu satu derajat saja dapat menurunkan daya tetas secara signifikan.\n\nSistem pemutaran telur otomatis sangat disarankan. Telur perlu diputar beberapa kali sehari agar embrio tidak menempel pada cangkang. Mesin dengan pemutar otomatis menghemat tenaga dan membuat hasil lebih konsisten, terutama bila peternak memiliki pekerjaan lain.\n\nTerakhir, perhatikan kemudahan pembersihan dan ketersediaan suku cadang. Mesin tetas bekerja dalam kondisi hangat dan lembap sehingga harus dibersihkan setiap selesai satu periode. Alat yang mudah dirawat akan berumur lebih panjang dan lebih aman dari penyakit.",
            ],
            [
                'category' => 'teknologi-pertanian',
                'title' => 'Mengenal Sensor IoT untuk Memantau Kelembapan Tanah',
                'excerpt' => 'Sensor kelembapan tanah membantu petani memutuskan kapan harus mengairi lahan, tanpa menebak. Begini cara kerja dan manfaatnya untuk sawah dan kebun.',
                'days_ago' => 7,
                'content' => "Keputusan pengairan sering diambil berdasarkan perkiraan: melihat permukaan tanah atau mengikuti jadwal tetap. Padahal kondisi di dalam tanah bisa sangat berbeda dari yang terlihat, dan di sinilah sensor Internet of Things atau IoT berperan.\n\nSensor kelembapan ditanam pada kedalaman akar dan mengukur kadar air tanah secara berkala. Data dikirim ke server melalui jaringan nirkabel, lalu ditampilkan sebagai grafik di dashboard atau aplikasi ponsel. Petani dapat melihat tren kelembapan dari hari ke hari dan menerima peringatan saat tanah mulai terlalu kering.\n\nManfaatnya terasa pada penghematan air dan tenaga. Pengairan hanya dilakukan saat tanaman benar-benar membutuhkan, sehingga pompa tidak bekerja berlebihan. Pada lahan yang luas, data sensor juga membantu menemukan petakan yang bermasalah, misalnya saluran air yang tersumbat.\n\nFunFarm sedang menyiapkan integrasi data sensor ke dalam dashboard monitoring, sehingga catatan lahan, biaya, dan kondisi tanah dapat dilihat dalam satu tempat. Teknologi ini bukan pengganti pengalaman petani, melainkan alat bantu agar keputusan di lapangan didukung data.",
            ],
            [
                'category' => 'informasi-umum',
                'title' => 'FunFarm Membuka Layanan Sewa Drone Semprot dan Mesin Tanam',
                'excerpt' => 'Petani kini dapat menyemprot dan menanam lebih cepat tanpa membeli alat mahal. FunFarm membuka layanan sewa drone semprot dan mesin tanam untuk musim tanam ini.',
                'days_ago' => 10,
                'content' => "Membeli drone semprot atau mesin tanam membutuhkan modal besar yang belum tentu sesuai untuk semua petani. Karena itu FunFarm membuka layanan penyewaan alat agar teknologi ini dapat dipakai bersama dengan biaya yang lebih terjangkau.\n\nDrone semprot mampu menyelesaikan penyemprotan satu hektare dalam waktu jauh lebih singkat dibanding cara manual, dengan sebaran cairan yang lebih merata. Sementara mesin tanam membantu penanaman bibit dengan jarak tanam yang rapi dan konsisten, sehingga pertumbuhan tanaman lebih seragam.\n\nPemesanan dilakukan melalui admin FunFarm dengan menyebutkan lokasi lahan, luas area, dan jadwal yang diinginkan. Pelanggan dengan status member mendapatkan harga khusus yang lebih rendah dari harga reguler, sesuai ketentuan yang berlaku.\n\nSetiap penyewaan dicatat dalam proyek pertanian pelanggan, lengkap dengan tanggal penggunaan dan biayanya. Dengan pencatatan ini, petani dapat melihat total biaya produksi per musim secara transparan dan menghitung hasil bersih setelah panen dengan lebih akurat.",
            ],
        ];

        foreach ($articles as $data) {
            Article::firstOrCreate(
                ['slug' => Article::uniqueSlug($data['title'])],
                [
                    'article_category_id' => $categories[$data['category']]->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'thumbnail' => null,
                    'status' => 'published',
                    'published_at' => Carbon::now()->subDays($data['days_ago']),
                ]
            );
        }
    }
}
