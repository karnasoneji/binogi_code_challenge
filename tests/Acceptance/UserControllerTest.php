<?php

namespace Tests\Acceptance;

use App\Models\User\User;
use App\Repositories\UserRepository;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\FrameworkTest;

class UserControllerTest extends FrameworkTest
{
    /** @var UserRepository */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
    }

    public function testShowRequestReturnsUserData()
    {
        /** @var User $user */
        $user   = $this->userFactory->create();
        $result = $this->get("/api/users/$user->id");
        $result->assertSuccessful();
        $this->assertEquals(
            [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'nickname' => $user->nickname
            ],
            json_decode($result->getContent(), true)
        );
    }

    public function testUpdateRequestUpdatesUserData()
    {
        /** @var User $user */
        $user   = $this->userFactory->create();
        $data = [
            'id'    => $user->id,
            'name'  => $this->faker->name,
            'email' => $user->email,
            'nickname' => $user->nickname
        ];
        $result = $this->put("/api/users/$user->id", $data);
        $result->assertSuccessful();
        $this->assertEquals($data, json_decode($result->getContent(), true));
    }

    public function testCreateRequestCreatesUser()
    {
        $data = [
            'name'     => $this->faker->name,
            'email'    => $email = $this->faker->unique()->email,
            'password' => 'hen rooster chicken duck',
        ];
        $this->assertFalse($this->repository->getModel()->newQuery()->where('email', $email)->exists());
        $result = $this->post("/api/users", $data);
        $result->assertSuccessful();
        $this->assertTrue($this->repository->getModel()->newQuery()->where('email', $email)->exists());
    }

    public function testNickNameIsOptionalInParameter()
    {
        /**
         *
         * Making sure Nick Name is optional as a parameter in store() method.
         *
         */

        $data = [
            'name'  => 'karna soneji',
            'email' => 'sonejikarna@gmail.com',
            'password' => 'test password',
            'nickname' => ''
        ];

        $result = $this->post("/api/users", $data);
        $result->assertSuccessful();

        /**
         *
         * Making sure Nick Name is optional as a parameter in update() method.
         *
         */
        $user = json_decode($result->getContent());

        $data = [
            'name'  => 'Swati Dubal',
            'nickname' => ''
        ];

        $result = $this->put("/api/users/$user->id", $data);
        $result->assertSuccessful();

    }

    public function testEmptyRequestBodyInPutMethodFailsValidation()
    {
        /**
         *
         * We want to test that when request body is empty, the validation fails.
         *
         */

        /** @var User $user */
        $user   = $this->userFactory->create();

        $data = [];

        $result = $this->put("/api/users/$user->id", $data);
        $result->assertSessionHasErrors(['name','email','password']);

    }

    public function testIntergerValueInNicknameFailsValidation()
    {
        /**
         *
         * We want to test that when integer value is passed as NickName, the validation fails.
         *
         */

        /** @var User $user */
        $user   = $this->userFactory->create();

        $data = ['nickname' => 12666262];

        $result = $this->put("/api/users/$user->id", $data);
        $result->assertSessionHasErrors(['nickname']);

    }

    public function testNicknameLengthMoreThanTwentyNineCharactersFailsValidation()
    {
        /**
         *
         * We want to test that when NickName is more than 29 characters, the validation fails.
         *
         */

        /** @var User $user */
        $user   = $this->userFactory->create();

        $data = ['nickname' => 'lalu soneji lalu soneji lalu lalu soneji lalu soneji lalu '];

        $result = $this->put("/api/users/$user->id", $data);

        $result->assertSessionHasErrors(['nickname']);

    }

    public function testNickNameMustBeUniqueAmongUsers()
    {
        /**
         *
         *  A valid nickname must be unique among users.
         *
         * */

        /** @var User $user */
        $user = $this->userFactory->create();

        $secondUser = $this->userFactory->create();

        $data = ['nickname' => $user->nickname];

        $result = $this->put("/api/users/$secondUser->id", $data);

        $result->assertSessionHasErrors(['nickname']);

    }

    public function testCustomExceptionMessageForAccessingInvalidRecord()
    {
        /**
         *
         * This test is to validate that when accessing or updating a non-existing record/resource, It returns a customized exception message
         *
         */

        $result = $this->getJson("/api/users/1");

        $result->assertJson(fn (AssertableJson $json) =>
                $json->where('error.message', "Resource not found.")
        );

        $data = [
            'name'  => 'karna soneji',
            'email' => 'sonejikarna@gmail.com',
            'password' => 'test password',
            'nickname' => ''
        ];

        $result = $this->put("/api/users/1", $data);

        $result->assertJson(fn (AssertableJson $json) =>
                $json->where('error.message', "Resource not found.")
        );
    }

}
