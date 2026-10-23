$src = "c:\jyoti\scholarborg\scholarborg\frontend\extracted_images"
$dest = "c:\jyoti\scholarborg\scholarborg\frontend\src\assets"

Copy-Item "$src\dreamy_schoolgirl_in_golden_classroom_light.png" "$dest\edu_hero_girl.png" -Force
Copy-Item "$src\dreaming_by_the_classroom_window.png" "$dest\edu_challenge_access.png" -Force
Copy-Item "$src\focused_classroom_study.png" "$dest\edu_challenge_affordability.png" -Force
Copy-Item "$src\thoughtful_student_in_fractions_class.png" "$dest\edu_challenge_support.png" -Force
Copy-Item "$src\focused_learning_digital_skills_classroom.png" "$dest\edu_challenge_digital.png" -Force
Copy-Item "$src\wide_shallow_depth_of_field_photo_of_a_classroom.png" "$dest\edu_challenge_financial.png" -Force
Copy-Item "$src\brighter_india_begins_in_the_classroom.png" "$dest\edu_response_students.png" -Force

Write-Host "SUCCESS: All 7 high-res images copied to $dest!"
Get-ChildItem "$dest\edu_*.png" | Select-Object Name, Length
