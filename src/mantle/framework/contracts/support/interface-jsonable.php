<?php

namespace Mantle\Framework\Contracts\Support;

interface Jsonable {
  /**
   * Convert the object to its JSON representation.
   *
   * @param  int  $options
   * @return string
   */
  public function to_json($options = 0 );
}
