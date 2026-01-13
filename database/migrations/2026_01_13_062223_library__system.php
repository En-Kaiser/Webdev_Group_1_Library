<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id'); 
            $table->string('name');
        });

        Schema::create('genres', function (Blueprint $table) {
            $table->id('genre_id');
            $table->string('name');
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id('author_id');
            $table->string('name');
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');
            $table->string('title');
            $table->integer('year');
            $table->string('short_description');
            $table->string('image');
        });

        
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('course_id')->constrained('courses', 'course_id');
            $table->timestamp('date_joined')->useCurrent(); 
            $table->timestamps(); 
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('user_accounts', 'user_id')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books', 'book_id')->cascadeOnDelete();
            $table->primary(['user_id', 'book_id']);
        });

        Schema::create('books_joint_authors', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained('books', 'book_id')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('authors', 'author_id')->cascadeOnDelete();

            $table->primary(['book_id', 'author_id']);
        });

        Schema::create('books_joint_genres', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained('books', 'book_id')->CascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres', 'genre_id')->CascadeOnDelete();

            $table->primary(['book_id', 'genre_id']);
        });

        Schema::create('history', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('user_id')->constrained('user_accounts', 'user_id');
            $table->foreignId('book_id')->constrained('books', 'book_id');
            $table->enum('type', ['physical', 'e_book']);
            $table->timestamp('date_borrowed')->useCurrent();
            $table->timestamp('date_return')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'due']);
        });

        Schema::create('book_type_avail', function (Blueprint $table) {
            $table->id('book_type_id')->CascadeOnDelete();
            $table->foreignId('book_id')->constrained('books', 'book_id');
            $table->enum('type', ['physical', 'e_book']);
            // Note: This part might be good, will check later
            // $table->integer('total_copies');
            // $table->integer('available_copies');
            $table->enum('availability', ['available', 'unavailable']);
        });

    
        $SeedLibraryProcedure = "
            DROP PROCEDURE IF EXISTS SeedLibraryData;

            CREATE PROCEDURE SeedLibraryData()
        BEGIN
        
            SET FOREIGN_KEY_CHECKS = 0;

            
            -- Call TruncateAllTables();


            INSERT INTO courses (name) VALUES 
            ('Computer Science'), ('Literature'), ('Engineering'), ('History');


            INSERT INTO genres (name) VALUES 
            ('Sci-Fi'), ('Technical'), ('Romance'), ('Fantasy'), ('Mystery');

            
            INSERT INTO authors (name) VALUES 
            ('George Orwell'), ('J.K. Rowling'), ('Robert C. Martin'), ('Jane Austen'), ('Isaac Asimov');

        
            INSERT INTO books (title, year, short_description, image) VALUES 
            ('1984', 1949, 'Dystopian social science fiction.', 'img_1984.jpg'),
            ('Clean Code', 2008, 'A Handbook of Agile Software Craftsmanship.', 'img_clean.jpg'),
            ('Pride and Prejudice', 1813, 'Romantic novel of manners.', 'img_pride.jpg'),
            ('Harry Potter 1', 1997, 'A wizard enters a school of magic.', 'img_hp1.jpg'),
            ('Foundation', 1951, 'The story of the collapse of an empire.', 'img_found.jpg'),
            ('The Hobbit', 1937, 'A fantasy novel about a quest.', 'img_hobbit.jpg'),
            ('The Pragmatic Programmer', 1999, 'Your journey to mastery.', 'img_prag.jpg'),
            ('Animal Farm', 1945, 'A beast fable.', 'img_animal.jpg'),
            ('Emma', 1815, 'Novel about youthful hubris.', 'img_emma.jpg'),
            ('Harry Potter 2', 1998, 'The Chamber of Secrets.', 'img_hp2.jpg'),
            ('I, Robot', 1950, 'Collection of science fiction short stories.', 'img_robot.jpg'),
            ('Design Patterns', 1994, 'Elements of Reusable Object-Oriented Software.', 'img_design.jpg'),
            ('Sense and Sensibility', 1811, 'A classic romance.', 'img_sense.jpg'),
            ('Harry Potter 3', 1999, 'The Prisoner of Azkaban.', 'img_hp3.jpg'),
            ('Neuromancer', 1984, 'One of the earliest cyberpunk novels.', 'img_neuro.jpg'),
            ('Refactoring', 1999, 'Improving the Design of Existing Code.', 'img_refactor.jpg');

            
            INSERT INTO books_joint_authors (book_id, author_id) VALUES 
            (1,1), (2,3), (3,4), (4,2), (5,5), (6,2), (7,3), (8,1), (9,4), (10,2), 
            (11,5), (12,3), (13,4), (14,2), (15,1), (16,3);


            INSERT INTO books_joint_genres (book_id, genre_id) VALUES 
            (1,1), (2,2), (3,3), (4,4), (5,1), (6,4), (7,2), (8,1), (9,3), (10,4), 
            (11,1), (12,2), (13,3), (14,4), (15,1), (16,2);

            
            INSERT INTO book_type_avail (book_id, type, availability) VALUES 
            (1, 'physical', 'available'),
            (2, 'e_book', 'available'),
            (3, 'physical', 'unavailable'),
            (4, 'physical', 'available'),
            (5, 'e_book', 'available'),
            (6, 'physical', 'unavailable');

            
            INSERT INTO user_accounts (email, password, course_id, date_joined) VALUES 
            ('john.doe@uni.edu', 'pass123', '1', NOW()),
            ('jane.smith@uni.edu', 'secure456', '2', NOW()),
            ('bob.jones@uni.edu', 'qwerty789', '3', NOW());

        
            INSERT INTO history (user_id, book_id, type, date_borrowed, date_return, status) VALUES 
            (1, 1, 'physical', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), 'Returned');

        
            INSERT INTO history (user_id, book_id, type, date_borrowed, date_return, status) VALUES 
            (2, 3, 'physical', DATE_SUB(NOW(), INTERVAL 2 DAY), NULL, 'Borrowed');

        
            INSERT INTO bookmarks (user_id, book_id) VALUES (1, 2), (1, 7), (2, 4);

            SET FOREIGN_KEY_CHECKS = 1;

        END;

            ";
        DB::unprepared($SeedLibraryProcedure);

        $TruncateAllTables = "
        DROP PROCEDURE IF EXISTS TruncateAllTables;
        CREATE PROCEDURE TruncateAllTables()
        BEGIN

            DECLARE done INT DEFAULT FALSE;
            DECLARE tableName VARCHAR(255);
            
            -- Cursor to select all actual tables (excluding Views) from the current database
            DECLARE cur CURSOR FOR 
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_type = 'BASE TABLE';
                
            -- Stop the loop when no more tables are found
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

            SET FOREIGN_KEY_CHECKS = 0;

            OPEN cur;

            read_loop: LOOP
                FETCH cur INTO tableName;
                
                IF done THEN
                    LEAVE read_loop;
                END IF;

                SET @stmt = CONCAT('TRUNCATE TABLE `', tableName, '`');
                PREPARE stmt FROM @stmt;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
                
            END LOOP;

            CLOSE cur;


            SET FOREIGN_KEY_CHECKS = 1;
        END;
        ";
        DB::unprepared($TruncateAllTables);

        $TruncateSingleTable = "
        DROP PROCEDURE IF EXISTS TruncateSingleTable;
        CREATE PROCEDURE TruncateSingleTable(IN tableName VARCHAR(255))
        BEGIN
            SET FOREIGN_KEY_CHECKS = 0;
            
            SET @cmd = CONCAT('TRUNCATE TABLE `', tableName, '`');
            PREPARE stmt FROM @cmd;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            SET FOREIGN_KEY_CHECKS = 1;
        END;
        ";

        DB::unprepared($TruncateSingleTable);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_type_avail');
        Schema::dropIfExists('history');
        Schema::dropIfExists('books_joint_genres');
        Schema::dropIfExists('books_joint_authors');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('user_accounts');
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('courses');

        DB::unprepared("DROP PROCEDURE IF EXISTS SeedLibraryData;");
        DB::unprepared("DROP PROCEDURE IF EXISTS TruncateAllTables;");
        DB::unprepared("DROP PROCEDURE IF EXISTS TruncateSingleTable;");
    }
};
