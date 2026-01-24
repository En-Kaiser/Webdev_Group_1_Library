<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Library_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('courses')->insert([
            ['name' => 'Computer Science'],
            ['name' => 'Literature'],
            ['name' => 'Engineering'],
            ['name' => 'History'],
        ]);

        DB::table('genres')->insert([
            ['name' => 'Sci-Fi'],
            ['name' => 'Technical'],
            ['name' => 'Romance'],
            ['name' => 'Fantasy'],
            ['name' => 'Mystery'],
        ]);

        DB::table('authors')->insert([
            ['name' => 'George Orwell'],
            ['name' => 'J.K. Rowling'],
            ['name' => 'Robert C. Martin'],
            ['name' => 'Jane Austen'],
            ['name' => 'Isaac Asimov'],
        ]);

        DB::table('books')->insert([
            ['book_id' => 1, 'title' => '1984', 'year' => 1949, 'short_description' => 'Dystopian social science fiction.', 'image' => 'img_1984.jpg'],
            ['book_id' => 2, 'title' => 'Clean Code', 'year' => 2008, 'short_description' => 'A Handbook of Agile Software Craftsmanship.', 'image' => 'img_clean.jpg'],
            ['book_id' => 3, 'title' => 'Pride and Prejudice', 'year' => 1813, 'short_description' => 'Romantic novel of manners.', 'image' => 'img_pride.jpg'],
            ['book_id' => 4, 'title' => 'Harry Potter 1', 'year' => 1997, 'short_description' => 'A wizard enters a school of magic.', 'image' => 'img_hp1.jpg'],
            ['book_id' => 5, 'title' => 'Foundation', 'year' => 1951, 'short_description' => 'The story of the collapse of an empire.', 'image' => 'img_found.jpg'],
            ['book_id' => 6, 'title' => 'The Hobbit', 'year' => 1937, 'short_description' => 'A fantasy novel about a quest.', 'image' => 'img_hobbit.jpg'],
            ['book_id' => 7, 'title' => 'The Pragmatic Programmer', 'year' => 1999, 'short_description' => 'Your journey to mastery.', 'image' => 'img_prag.jpg'],
            ['book_id' => 8, 'title' => 'Animal Farm', 'year' => 1945, 'short_description' => 'A beast fable.', 'image' => 'img_animal.jpg'],
            ['book_id' => 9, 'title' => 'Emma', 'year' => 1815, 'short_description' => 'Novel about youthful hubris.', 'image' => 'img_emma.jpg'],
            ['book_id' => 10, 'title' => 'Harry Potter 2', 'year' => 1998, 'short_description' => 'The Chamber of Secrets.', 'image' => 'img_hp2.jpg'],
            ['book_id' => 11, 'title' => 'I, Robot', 'year' => 1950, 'short_description' => 'Collection of science fiction short stories.', 'image' => 'img_robot.jpg'],
            ['book_id' => 12, 'title' => 'Design Patterns', 'year' => 1994, 'short_description' => 'Elements of Reusable Object-Oriented Software.', 'image' => 'img_design.jpg'],
            ['book_id' => 13, 'title' => 'Sense and Sensibility', 'year' => 1811, 'short_description' => 'A classic romance.', 'image' => 'img_sense.jpg'],
            ['book_id' => 14, 'title' => 'Harry Potter 3', 'year' => 1999, 'short_description' => 'The Prisoner of Azkaban.', 'image' => 'img_hp3.jpg'],
            ['book_id' => 15, 'title' => 'Neuromancer', 'year' => 1984, 'short_description' => 'One of the earliest cyberpunk novels.', 'image' => 'img_neuro.jpg'],
            ['book_id' => 16, 'title' => 'Refactoring', 'year' => 1999, 'short_description' => 'Improving the Design of Existing Code.', 'image' => 'img_refactor.jpg'],
        ]);


        DB::table('books_joint_authors')->insert([
            ['book_id' => 1, 'author_id' => 1],
            ['book_id' => 2, 'author_id' => 3],
            ['book_id' => 3, 'author_id' => 4],
            ['book_id' => 4, 'author_id' => 2],
            ['book_id' => 5, 'author_id' => 5],
            ['book_id' => 6, 'author_id' => 2],
            ['book_id' => 7, 'author_id' => 3],
            ['book_id' => 8, 'author_id' => 1],
            ['book_id' => 9, 'author_id' => 4],
            ['book_id' => 10, 'author_id' => 2],
            ['book_id' => 11, 'author_id' => 5],
            ['book_id' => 12, 'author_id' => 3],
            ['book_id' => 13, 'author_id' => 4],
            ['book_id' => 14, 'author_id' => 2],
            ['book_id' => 15, 'author_id' => 1],
            ['book_id' => 16, 'author_id' => 3],
        ]);


        DB::table('books_joint_genres')->insert([
            ['book_id' => 1, 'genre_id' => 1],
            ['book_id' => 2, 'genre_id' => 2],
            ['book_id' => 3, 'genre_id' => 3],
            ['book_id' => 4, 'genre_id' => 4],
            ['book_id' => 5, 'genre_id' => 1],
            ['book_id' => 6, 'genre_id' => 4],
            ['book_id' => 7, 'genre_id' => 2],
            ['book_id' => 8, 'genre_id' => 1],
            ['book_id' => 9, 'genre_id' => 3],
            ['book_id' => 10, 'genre_id' => 4],
            ['book_id' => 11, 'genre_id' => 1],
            ['book_id' => 12, 'genre_id' => 2],
            ['book_id' => 13, 'genre_id' => 3],
            ['book_id' => 14, 'genre_id' => 4],
            ['book_id' => 15, 'genre_id' => 1],
            ['book_id' => 16, 'genre_id' => 2],
        ]);

        DB::table('book_type_avail')->insert([
            ['book_id' => 1, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 1, 'type' => 'e_book', 'availability' => 'unavailable'],
            ['book_id' => 2, 'type' => 'physical', 'availability' => 'unavailable'],
            ['book_id' => 2, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 3, 'type' => 'physical', 'availability' => 'unavailable'],
            ['book_id' => 3, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 4, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 4, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 5, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 5, 'type' => 'e_book', 'availability' => 'unavailable'],
            ['book_id' => 6, 'type' => 'physical', 'availability' => 'unavailable'],
            ['book_id' => 6, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 7, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 7, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 8, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 8, 'type' => 'e_book', 'availability' => 'unavailable'],
            ['book_id' => 9, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 9, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 10, 'type' => 'physical', 'availability' => 'unavailable'],
            ['book_id' => 10, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 11, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 11, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 12, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 12, 'type' => 'e_book', 'availability' => 'unavailable'],
            ['book_id' => 13, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 13, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 14, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 14, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 15, 'type' => 'physical', 'availability' => 'unavailable'],
            ['book_id' => 15, 'type' => 'e_book', 'availability' => 'available'],
            ['book_id' => 16, 'type' => 'physical', 'availability' => 'available'],
            ['book_id' => 16, 'type' => 'e_book', 'availability' => 'available'],
        ]);

        DB::table('user_accounts')->insert([
            [
                'first_name' => 'John', 
                'last_name' => 'Doe', 
                'email' => 'john.doe@uni.edu', 
                'password' => Hash::make('pass123'), 
                'course_id' => 1, 
                'date_joined' => now(),
                'role' => 'student' 
            ],
            [
                'first_name' => 'Jane', 
                'last_name' => 'Smith', 
                'email' => 'jane.smith@uni.edu', 
                'password' => Hash::make('secure456'), 
                'course_id' => 2, 
                'date_joined' => now(),
                'role' => 'student'
            ],
            [
                'first_name' => 'Bob', 
                'last_name' => 'Jones', 
                'email' => 'bob.jones@uni.edu', 
                'password' => Hash::make('qwerty789'), 
                'course_id' => 3, 
                'date_joined' => now(),
                'role' => 'student'
            ],
        ]);
    }
}
