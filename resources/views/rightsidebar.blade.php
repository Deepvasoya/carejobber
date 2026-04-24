  <div class="col-lg-2" style="flex: 0 0 auto; ">

      <!-- Top Employers Box -->
      <div class="sidebar-box top-employers-box" style="background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
          <h4 style="margin: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 600; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
              <i class="fas fa-building" style="color: #667eea; margin-right: 8px;"></i>{{__('Top Employers')}}
          </h4>
          @php
          $topEmployers = \App\Company::where('is_active', 1)
          ->where('is_featured', 1)
          ->withCount(['jobs' => function($query) {
          $query->where('is_active', 1);
          }])
          ->orderBy('jobs_count', 'desc')
          ->limit(5)
          ->get();
          @endphp
          @if($topEmployers->count() > 0)
          <ul style="list-style: none; padding: 0; margin: 0;">
              @foreach($topEmployers as $employer)
              <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                  <a href="{{route('company.detail', $employer->slug)}}" style="display: flex; align-items: center; text-decoration: none; color: #333;">
                      <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; margin-right: 12px; flex-shrink: 0; border: 1px solid #e0e0e0;">
                          {{$employer->printCompanyImage(50, 50)}}
                      </div>
                      <div style="flex: 1; min-width: 0;">
                          <h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{$employer->name}}</h5>
                          <p style="margin: 0; font-size: 12px; color: #666;">
                              <i class="fas fa-briefcase" style="font-size: 10px;"></i> {{$employer->jobs_count}} {{__('Open Jobs')}}
                          </p>
                      </div>
                  </a>
              </li>
              @endforeach
          </ul>
          <a href="{{route('company.listing')}}" style="display: block; text-align: center; margin-top: 15px; color: #667eea; font-weight: 600; font-size: 14px; text-decoration: none;">
              {{__('View All Employers')}} <i class="fas fa-arrow-right"></i>
          </a>
          @else
          <p style="text-align: center; color: #999; font-size: 14px; margin: 20px 0;">{{__('No employers found')}}</p>
          @endif
      </div>

      <!-- Mobile App CTA -->
      <div class="sidebar-box mobile-app-cta" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); text-align: center; color: #fff;">
          <div style="font-size: 48px; margin-bottom: 15px;">
              <i class="fas fa-mobile-alt"></i>
          </div>
          <h4 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600; color: #fff;">{{__('Download Medojob App')}}</h4>
          <p style="margin: 0 0 20px 0; font-size: 14px; color: rgba(255,255,255,0.9);">{{__('Find jobs on the go! Download our mobile app now.')}}</p>
          <div style="display: flex; flex-direction: column; gap: 10px;">
              <a href="#" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                  <i class="fab fa-apple"></i> {{__('App Store')}}
              </a>
              <a href="#" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                  <i class="fab fa-google-play"></i> {{__('Google Play')}}
              </a>
          </div>
      </div>

      <!-- Social Media CTA -->
      <div class="sidebar-box social-cta" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
          <h4 style="margin: 0 0 15px 0; font-size: 18px; color: #333; font-weight: 600;">{{__('Follow Medojob')}}</h4>
          <p style="margin: 0 0 20px 0; font-size: 14px; color: #666;">{{__('Stay connected with us on social media')}}</p>
          <div style="display: flex; justify-content: center; gap: 15px;">
              <a href="https://facebook.com/medojob" target="_blank" style="width: 45px; height: 45px; border-radius: 50%; background: #1877f2; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 20px; transition: transform 0.3s;">
                  <i class="fab fa-facebook-f"></i>
              </a>
              <a href="https://linkedin.com/company/medojob" target="_blank" style="width: 45px; height: 45px; border-radius: 50%; background: #0077b5; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 20px; transition: transform 0.3s;">
                  <i class="fab fa-linkedin-in"></i>
              </a>
              <a href="https://youtube.com/@medojob" target="_blank" style="width: 45px; height: 45px; border-radius: 50%; background: #ff0000; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 20px; transition: transform 0.3s;">
                  <i class="fab fa-youtube"></i>
              </a>
          </div>
      </div>

  </div>