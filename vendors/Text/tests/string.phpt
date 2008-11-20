--TEST--
Text_Diff: Text_Diff_Engine_string test.
--FILE--
<?php
include_once 'Text/Diff.php';

$unified = file_get_contents(dirname(__FILE__) . '/unified.patch');
$context = file_get_contents(dirname(__FILE__) . '/context.patch');

$diff_u = &new Text_Diff('string', array($unified));
$diff_c = &new Text_Diff('string', array($context));
echo strtolower(print_r($diff_u, true));
echo strtolower(print_r($diff_c, true));
?>
--EXPECT--
text_diff object
(
    [_edits] => array
        (
            [0] => text_diff_op_copy object
                (
                    [orig] => array
                        (
                            [0] => this line is the same.
                        )

                    [final] => array
                        (
                            [0] => this line is the same.
                        )

                )

            [1] => text_diff_op_change object
                (
                    [orig] => array
                        (
                            [0] => this line is different in 1.txt
                        )

                    [final] => array
                        (
                            [0] => this line is different in 2.txt
                        )

                )

            [2] => text_diff_op_copy object
                (
                    [orig] => array
                        (
                            [0] => this line is the same.
                        )

                    [final] => array
                        (
                            [0] => this line is the same.
                        )

                )

        )

)
text_diff object
(
    [_edits] => array
        (
            [0] => text_diff_op_copy object
                (
                    [orig] => array
                        (
                            [0] => this line is the same.
                        )

                    [final] => array
                        (
                            [0] => this line is the same.
                        )

                )

            [1] => text_diff_op_change object
                (
                    [orig] => array
                        (
                            [0] => this line is different in 1.txt
                        )

                    [final] => array
                        (
                            [0] => this line is different in 2.txt
                        )

                )

            [2] => text_diff_op_copy object
                (
                    [orig] => array
                        (
                            [0] => this line is the same.
                        )

                    [final] => array
                        (
                            [0] => this line is the same.
                        )

                )

        )

)