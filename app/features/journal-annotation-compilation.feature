Feature: Journal annotation compilation

  In order to keep an organised collection of code change logs
  As a developer
  I need to be able to have source code files scanned and any journal annotations compiled

  Scenario: Compiling annotations from a directory with a single file in it
    Given the date-time is "2019-04-29 16:54:00"
    And there is a file "/foo/bar.php"
    And the file "/foo/bar.php" has a journal annotation saying:
      """
      Hello World!
      """
    When I run the journal annotation compile command on the path "/foo"
    Then there should be an annotation compiled from "/foo/bar.php" at "2019-04-29 16:54:00" saying:
      """
      Hello World!
      """