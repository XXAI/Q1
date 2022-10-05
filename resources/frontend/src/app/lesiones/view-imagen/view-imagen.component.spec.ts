import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewImagenComponent } from './view-imagen.component';

describe('ViewImagenComponent', () => {
  let component: ViewImagenComponent;
  let fixture: ComponentFixture<ViewImagenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ViewImagenComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ViewImagenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
