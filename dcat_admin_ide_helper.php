<?php

/**
 * A helper file for Dcat Admin, to provide autocomplete information to your IDE
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author jqh <841324345@qq.com>
 */
namespace Dcat\Admin {
    use Illuminate\Support\Collection;

    /**
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection account
     * @property Grid\Column|Collection address_id
     * @property Grid\Column|Collection background_image_url
     * @property Grid\Column|Collection birth
     * @property Grid\Column|Collection birth_day
     * @property Grid\Column|Collection collected_illust_date
     * @property Grid\Column|Collection comment
     * @property Grid\Column|Collection country_code
     * @property Grid\Column|Collection deleted_at
     * @property Grid\Column|Collection gender
     * @property Grid\Column|Collection is_collected_illust
     * @property Grid\Column|Collection is_collected_image
     * @property Grid\Column|Collection is_priority_collect
     * @property Grid\Column|Collection job
     * @property Grid\Column|Collection job_id
     * @property Grid\Column|Collection pixiv_id
     * @property Grid\Column|Collection profile_image_urls
     * @property Grid\Column|Collection region
     * @property Grid\Column|Collection total_follow_users
     * @property Grid\Column|Collection total_mypixiv_users
     * @property Grid\Column|Collection twitter_account
     * @property Grid\Column|Collection twitter_url
     * @property Grid\Column|Collection webpage
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection key
     * @property Grid\Column|Collection remarks
     * @property Grid\Column|Collection email_verified_at
     *
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection account(string $label = null)
     * @method Grid\Column|Collection address_id(string $label = null)
     * @method Grid\Column|Collection background_image_url(string $label = null)
     * @method Grid\Column|Collection birth(string $label = null)
     * @method Grid\Column|Collection birth_day(string $label = null)
     * @method Grid\Column|Collection collected_illust_date(string $label = null)
     * @method Grid\Column|Collection comment(string $label = null)
     * @method Grid\Column|Collection country_code(string $label = null)
     * @method Grid\Column|Collection deleted_at(string $label = null)
     * @method Grid\Column|Collection gender(string $label = null)
     * @method Grid\Column|Collection is_collected_illust(string $label = null)
     * @method Grid\Column|Collection is_collected_image(string $label = null)
     * @method Grid\Column|Collection is_priority_collect(string $label = null)
     * @method Grid\Column|Collection job(string $label = null)
     * @method Grid\Column|Collection job_id(string $label = null)
     * @method Grid\Column|Collection pixiv_id(string $label = null)
     * @method Grid\Column|Collection profile_image_urls(string $label = null)
     * @method Grid\Column|Collection region(string $label = null)
     * @method Grid\Column|Collection total_follow_users(string $label = null)
     * @method Grid\Column|Collection total_mypixiv_users(string $label = null)
     * @method Grid\Column|Collection twitter_account(string $label = null)
     * @method Grid\Column|Collection twitter_url(string $label = null)
     * @method Grid\Column|Collection webpage(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection key(string $label = null)
     * @method Grid\Column|Collection remarks(string $label = null)
     * @method Grid\Column|Collection email_verified_at(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection id
     * @property Show\Field|Collection name
     * @property Show\Field|Collection type
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection version
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection order
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection password
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection username
     * @property Show\Field|Collection account
     * @property Show\Field|Collection address_id
     * @property Show\Field|Collection background_image_url
     * @property Show\Field|Collection birth
     * @property Show\Field|Collection birth_day
     * @property Show\Field|Collection collected_illust_date
     * @property Show\Field|Collection comment
     * @property Show\Field|Collection country_code
     * @property Show\Field|Collection deleted_at
     * @property Show\Field|Collection gender
     * @property Show\Field|Collection is_collected_illust
     * @property Show\Field|Collection is_collected_image
     * @property Show\Field|Collection is_priority_collect
     * @property Show\Field|Collection job
     * @property Show\Field|Collection job_id
     * @property Show\Field|Collection pixiv_id
     * @property Show\Field|Collection profile_image_urls
     * @property Show\Field|Collection region
     * @property Show\Field|Collection total_follow_users
     * @property Show\Field|Collection total_mypixiv_users
     * @property Show\Field|Collection twitter_account
     * @property Show\Field|Collection twitter_url
     * @property Show\Field|Collection webpage
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection key
     * @property Show\Field|Collection remarks
     * @property Show\Field|Collection email_verified_at
     *
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection account(string $label = null)
     * @method Show\Field|Collection address_id(string $label = null)
     * @method Show\Field|Collection background_image_url(string $label = null)
     * @method Show\Field|Collection birth(string $label = null)
     * @method Show\Field|Collection birth_day(string $label = null)
     * @method Show\Field|Collection collected_illust_date(string $label = null)
     * @method Show\Field|Collection comment(string $label = null)
     * @method Show\Field|Collection country_code(string $label = null)
     * @method Show\Field|Collection deleted_at(string $label = null)
     * @method Show\Field|Collection gender(string $label = null)
     * @method Show\Field|Collection is_collected_illust(string $label = null)
     * @method Show\Field|Collection is_collected_image(string $label = null)
     * @method Show\Field|Collection is_priority_collect(string $label = null)
     * @method Show\Field|Collection job(string $label = null)
     * @method Show\Field|Collection job_id(string $label = null)
     * @method Show\Field|Collection pixiv_id(string $label = null)
     * @method Show\Field|Collection profile_image_urls(string $label = null)
     * @method Show\Field|Collection region(string $label = null)
     * @method Show\Field|Collection total_follow_users(string $label = null)
     * @method Show\Field|Collection total_mypixiv_users(string $label = null)
     * @method Show\Field|Collection twitter_account(string $label = null)
     * @method Show\Field|Collection twitter_url(string $label = null)
     * @method Show\Field|Collection webpage(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection key(string $label = null)
     * @method Show\Field|Collection remarks(string $label = null)
     * @method Show\Field|Collection email_verified_at(string $label = null)
     */
    class Show {}

    /**
     
     */
    class Form {}

}

namespace Dcat\Admin\Grid {
    /**
     
     */
    class Column {}

    /**
     
     */
    class Filter {}
}

namespace Dcat\Admin\Show {
    /**
     
     */
    class Field {}
}
