<?php

class ForumTest extends MyBBIntegratorTestCase {
	
	const PASSWORDED_FORUM_ID = 3; 
	const NON_PASSWORDED_FORUM_ID = 2;

	const WRONG_PASSWORD = "wrong_password";
	const CORRECT_PASSWORD = "passworded";

	const CATEGORY_SYMBOL = 'c';

	const FORUM_TO_REMOVE_ID = 5;
	const FORUM_DOES_NOT_EXIST_ID = 999;

	const NEW_CATEGORY_NAME = "TestCreateCategory-Name";
	const NEW_CATEGORY_DESCRIPTION = "TestCreateCategory-Description";
	const NEW_CATEGORY_ID = 6;
	
	/**
	 * Test for checking forum password
	*/
	public function testCheckForumPasswordForPasswordedForum() {
		$this->assertFalse(
			$this->mybb_integrator->checkForumPassword(self::PASSWORDED_FORUM_ID, self::WRONG_PASSWORD), 
			"passworded forum should be false for wrong password"
		);
		$this->assertTrue(
			$this->mybb_integrator->checkForumPassword(self::PASSWORDED_FORUM_ID, self::CORRECT_PASSWORD),
			"passworded forum should be true for correct password"
		);
	}

	/**
	 * Testing that check for password for non-passworded forum is TRUE
	*/
	public function testCheckForumPasswordForNonPasswordedForum() {
		$this->assertTrue(
			$this->mybb_integrator->checkForumPassword(self::NON_PASSWORDED_FORUM_ID, self::WRONG_PASSWORD),
			"non-passworded forum should be true for any password"
		);
		$this->assertTrue(
			$this->mybb_integrator->checkForumPassword(self::NON_PASSWORDED_FORUM_ID, self::CORRECT_PASSWORD),
			"non-passworded forum should be true for any password"
		);
	}

	/**
	 * Test creating a category.
	 * This test is located in this class because in MyBB, a category is just a special Forum (only type differs)
	*/
	public function testCreateCategory() {
		$category_data = array(
			'name' => self::NEW_CATEGORY_NAME
		);
		$return_data = $this->mybb_integrator->createCategory($category_data);
		$this->assertTrue(
			count($return_data) > count($category_data),
			"Returning array should have more entries than the original parameter due to populating missing keys"
		);

		$this->assertEquals(
			self::NEW_CATEGORY_ID,
			$return_data['fid']
		);

		$this->assertEquals(
			self::NEW_CATEGORY_ID,
			$return_data['parentlist'],
			"parentlist should be the category id itself because it is a top level category"
		);

		$this->assertEquals(
			self::CATEGORY_SYMBOL,
			$return_data['type'],
			"type should be \"c\" for [c]ategory"
		);
	}

	public function testCreateCategoryWithNegativePid() {
		$category_data = array(
			'name' => self::NEW_CATEGORY_NAME,
			'pid' => -4
		);
		$return_data = $this->mybb_integrator->createCategory($category_data);

		$this->assertEquals(
			0,
			$return_data['pid']
		);
	}

	public function testRemoveForumWhichDoesNotExist() {
		$forum = $this->mybb_integrator->getForum(self::FORUM_DOES_NOT_EXIST_ID);
		$this->assertFalse(
			$forum,
			"forum should not exist and should therefore be false"
		);

		$status = $this->mybb_integrator->removeForumOrCategory(self::FORUM_DOES_NOT_EXIST_ID);

		$this->assertFalse(
			$status,
			"state of removing a forum which does not exist should be false"
		);
	}

	public function testRemoveForumWhichExists() {
		$forum = $this->mybb_integrator->getForum(self::FORUM_TO_REMOVE_ID);
		$this->assertTrue(
			is_array($forum),
			"forum should exist before removing it - therefore should be array"
		);

		$status = $this->mybb_integrator->removeForumOrCategory(self::FORUM_TO_REMOVE_ID);

		$this->assertTrue(
			$status,
			"state of removing forum should be true"
		);

		$forum = $this->mybb_integrator->getForum(self::FORUM_TO_REMOVE_ID);
		$this->assertFalse(
			$forum,
			"forum should not exist after removing it"
		);
	}
}