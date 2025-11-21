<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'instructor_id' => 4,
                'title' => 'Docker',
                'description' => 'Belajar Docker praktis di MacOs.',
                'price' => 69000,
                'thumbnail' => 'thumbnails/docker.jpg'
            ],
            [
                'instructor_id' => 4,
                'title' => 'Python',
                'description' => 'Belajar Python praktis, dari awal hingga Deep Learning.',
                'price' => 100000,
                'thumbnail' => 'thumbnails/python.jpg'
            ],
            [
                'instructor_id' => 3,
                'title' => 'React.js',
                'description' => 'Belajar React.js dari dasar hingga mahir, membangun aplikasi interaktif dan modern.',
                'price' => 47000,
                'thumbnail' => 'thumbnails/reactjs.jpg'
            ],
            [
                'instructor_id' => 3,
                'title' => 'Next.js',
                'description' => 'Pelajari dasar-dasar Next.js, framework React untuk membangun aplikasi web cepat, SEO-friendly, dan modern.',
                'price' => 96900,
                'thumbnail' => 'thumbnails/nextjs.jpg'
            ],
            [
                'instructor_id' => 4,
                'title' => 'Kubernetes',
                'description' => 'Kuasai Kubernetes untuk orkestrasi container dan membangun aplikasi cloud-native.',
                'price' => 120000,
                'thumbnail' => 'thumbnails/kubernetes.jpg'
            ],
            [
                'instructor_id' => 3,
                'title' => 'Pemrograman (Coding)',
                'description' => 'Belajar dasar-dasar pemrograman untuk membangun logika dan algoritma yang kuat.',
                'price' => 12000,
                'thumbnail' => 'thumbnails/ngoding.jpg'
            ],
            [
                'instructor_id' => 3,
                'title' => 'Php',
                'description' => 'Belajar PHP dari dasar hingga membuat website dinamis dan interaktif.',
                'price' => 13000,
                'thumbnail' => 'thumbnails/phpgajah.jpg'
            ],
            [
                'instructor_id' => 4,
                'title' => 'Go Programming',
                'description' => 'Pengenalan Golang: cepat, sederhana, dan cocok untuk membangun backend yang scalable.',
                'price' => 55000,
                'thumbnail' => 'thumbnails/golang.jpg'
            ]
            // Bisa ditambahkan course lain sesuai kebutuhan
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
