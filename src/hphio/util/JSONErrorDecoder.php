<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 9/30/18
 * Time: 5:08 PM
 * @author Michael Munger <mj@hph.io>
 */

namespace hphio\util;


class JSONErrorDecoder
{
    public static function decodeError($error) {
        if($error === JSON_ERROR_NONE                 ) return "No error has occurred";
        if($error === JSON_ERROR_DEPTH                ) return "The maximum stack depth has been exceeded";
        if($error === JSON_ERROR_STATE_MISMATCH       ) return "Invalid or malformed JSON";
        if($error === JSON_ERROR_CTRL_CHAR            ) return "Control character error, possibly incorrectly encoded";
        if($error === JSON_ERROR_SYNTAX               ) return "Syntax error";
        if($error === JSON_ERROR_UTF8                 ) return "Malformed UTF-8 characters, possibly incorrectly encoded";
        if($error === JSON_ERROR_RECURSION            ) return "One or more recursive references in the value to be encoded";
        if($error === JSON_ERROR_INF_OR_NAN           ) return "One or more NAN or INF values in the value to be encoded";
        if($error === JSON_ERROR_UNSUPPORTED_TYPE     ) return "A value of a type that cannot be encoded was given";
        if($error === JSON_ERROR_INVALID_PROPERTY_NAME) return "A property name that cannot be encoded was given";
        if($error === JSON_ERROR_UTF16                ) return "Malformed UTF-16 characters, possibly incorrectly encoded";
    }
}