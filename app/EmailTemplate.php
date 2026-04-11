<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Parse shortcodes in the template
     */
    public function parseShortcodes($data = [])
    {
        $body = $this->body;
        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $shortcode = '{' . strtoupper($key) . '}';
            $body = str_replace($shortcode, $value, $body);
            $subject = str_replace($shortcode, $value, $subject);
        }

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }

    /**
     * Get shortcodes as array
     */
    public function getShortcodesArray()
    {
        if (empty($this->shortcodes)) {
            return [];
        }
        return json_decode($this->shortcodes, true) ?? [];
    }
}
