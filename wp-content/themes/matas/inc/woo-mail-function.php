<?php
    if( get_theme_mod('woo_remove_cart_active') == 1 ){
        function ss_cart_updated( $cart_item_key, $cart ) {

            if(is_user_logged_in()){

                $user_id = get_current_user_id();

                $current_user = wp_get_current_user();

                $user_email = $current_user->user_email;

                $product_id = $cart->cart_contents[ $cart_item_key ]['product_id']; 
                $cart_data_info = get_post_meta( $product_id, 'more_info_data', true );


                $subject = 'Product removed from Cart page - '.get_bloginfo('name');
                $body = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <!-- START HEADER/BANNER -->
                        <tbody>
                        <tr>
                            <td align="center">
                                    <table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" valign="top" bgcolor="#f8cc65" style="background-size:cover;" width="600" height="100%">
                                                <img src="'.get_theme_mod('site_main_logo').'" alt="'.get_bloginfo('name').'">
                                            </td>
                                        </tr>
                                    
                                    </table>
                            </td>
                        </tr>
                <!-- END HEADER/BANNER -->
                <!--main content-->
                        <tr>
                            <td align="center">
                            <table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#ffffff;">
                                <tr>
                                    <td height="50" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td width="35"></td>
                                    <td style="font-family: Arial, Helvetica, sans-serif;color:#000;font-size:14px;line-height:25px;">
                                        Hello '.$current_user->first_name.' '.$current_user->last_name.' !
                                    </td>
                                    <td width="35"></td>
                                </tr>
                                        <tr>
                                    <td height="15" colspan="3"></td>
                                </tr>				
                                <tr>
                                    <td width="35"></td>
                                    <td style="font-family: Arial, Helvetica, sans-serif;color:#000;font-size:14px;line-height:25px;">
                                        You have removed product from Cart it.
                                        Product Name:- <a href="'.get_the_permalink($product_id).'">'.get_the_title($product_id).'<a/>
                                    </td>
                                    <td width="35"></td>
                                </tr>
                                <tr>
                                    <td width="35"></td>
                                    <td style="font-family: Arial, Helvetica, sans-serif;color:#000;font-size:14px;line-height:25px;">
                                        
                                    </td>
                                    <td width="35"></td>
                                </tr>
                                <tr>
                                    <td height="15" colspan="3"></td>
                                </tr>
                                    <tr>
                                    <td width="35"></td>
                                    <td style="font-family: Arial, Helvetica, sans-serif;color:#000;font-size:14px;line-height:25px;">
                                        Regards,
                                    </td>
                                    <td width="35"></td>
                                </tr>
                                <tr>
                                    <td width="35"></td>
                                    <td style="font-family: Arial, Helvetica, sans-serif;color:#000;font-size:14px;line-height:25px;">
                                    '.get_bloginfo('name').'
                                    </td>
                                    <td width="35"></td>
                                </tr>
                                <tr>
                                    <td height="50" colspan="3"></td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                
                <!--end main content-->
                <!--fotter-->
                <tr>
                    <td align="center">
                            <table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#f8cc65;">
                                <tr>
                                    <td height="15"></td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" style="font-family: Arial, Helvetica, sans-serif;color:#000000;font-size:14px;line-height:20px;">
                                    '.get_theme_mod('site_copyright_section').'
                                    </td>
                                </tr>
                                <tr>
                                    <td height="15"></td>
                                </tr>
                            </table>
                    </td>
                </tr>
                <!--end fotter-->				
                </tbody></table>';




                
                $headers = array('Content-Type: text/html; charset=UTF-8','From: '.get_bloginfo().' <'.get_option( 'admin_email' ).'>');
                //$body = 'Cart Removed '.$product_id;
                wp_mail($user_email, $subject,$body,$headers);
            }

        };
        add_action( 'woocommerce_remove_cart_item', 'ss_cart_updated', 10, 2 );
    }