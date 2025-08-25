<?php

// Add use statements for the models we need to create
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });


        $user = User::factory()->create([
            'name' => 'Default User',
            'email' => 'user@example.com',
        ]);

        $this->seedDefaultCategories($user->id);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }

    /**
     * A helper function to seed the default categories for a given user.
     */
    private function seedDefaultCategories(int $userId): void
    {
        $categories = [
            'Income' => ['Salary', 'Bonus', 'Freelance'],
            'Housing' => ['Rent/Mortgage', 'Utilities', 'Maintenance'],
            'Food' => ['Groceries', 'Restaurants', 'Coffee Shops'],
            'Transportation' => ['Gas/Fuel', 'Public Transit', 'Ride Sharing'],
            'Personal Care' => ['Haircut', 'Toiletries', 'Subscriptions'],
            'Entertainment' => ['Movies', 'Concerts', 'Hobbies'],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::create([
                'user_id' => $userId,
                'name' => $parentName,
                'parent_id' => null,
            ]);

            foreach ($children as $childName) {
                Category::create([
                    'user_id' => $userId,
                    'name' => $childName,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
};